<?php
/**
 * Register SCE Options for front-end editing.
 *
 * @package SCEOptions
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}

/**
 * Main class for front-end editing.
 */
class SCE_Frontend_Editing {

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'setup_ajax_calls' ) );
		add_action( 'init', array( $this, 'setup_comment_filters' ) );
	}

	/**
	 * Set up front-end editing filters.
	 */
	public function setup_comment_filters() {
		include_once SCE_Options::get_instance()->get_plugin_dir( 'includes/class-sce-options.php' );
		$sce_options = new SCE_Plugin_Options();
		$options     = $sce_options->get_options();
		/* Begin Filters */
		if ( ! is_feed() && ! defined( 'DOING_SCE' ) && true === filter_var( $options['allow_front_end_editing'], FILTER_VALIDATE_BOOLEAN ) ) {
			if ( current_user_can( 'moderate_comments' ) ) {
				add_filter( 'comment_excerpt', array( $this, 'add_edit_interface' ), 1000, 2 );
				add_filter( 'comment_text', array( $this, 'add_edit_interface' ), 1000, 2 );
				add_filter( 'thesis_comment_text', array( $this, 'add_edit_interface' ), 1000, 2 );
				add_filter( 'sce_can_edit', '__return_false' );
				add_filter( 'edit_comment_link', array( $this, 'modify_edit_link' ), 10, 3 );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_and_styles' ) );
				add_action( 'wp_ajax_sce_options_get_frontend_comment', array( $this, 'ajax_fancybox_interface' ) );
				add_action( 'wp_ajax_sce_frontend_save_comment', array( $this, 'ajax_save_frontend_comment' ) );
				add_action( 'wp_ajax_sce_frontend_delete_comment', array( $this, 'ajax_delete_frontend_comment' ) );
			}
		}
	}

	/**
	 * Get the HTML for FancyBox Edit via Ajax
	 */
	public function ajax_fancybox_interface() {
		$nonce = sanitize_text_field( $_GET['nonce'] ); // phpcs:ignore
		$comment_id = absint( $_GET['cid'] ); // phpcs:ignore
		if ( ! current_user_can( 'moderate_comments' ) ) {
			wp_die( 'The user must be able to edit comments.' );
		}
		if ( ! wp_verify_nonce( $nonce, 'edit-comment-' . $comment_id ) ) {
			wp_die( 'Could not validate nonce.' );
		}
		include_once SCE_Options::get_instance()->get_plugin_dir( '/templates/front-end-editing.php' );
		die( '' );
	}

	/**
	 * Save a comment via Ajax.
	 */
	public function ajax_save_frontend_comment() {
		$nonce = sanitize_text_field( $_POST['nonce'] ); // phpcs:ignore
		$comment_id = absint( $_POST['comment_id'] ); // phpcs:ignore
		if ( ! current_user_can( 'moderate_comments' ) ) {
			die( 'The user must be able to edit comments.' );
		}
		if ( ! wp_verify_nonce( $nonce, 'sce-modify-comment-' . $comment_id ) ) {
			die( 'Could not validate nonce.' );
		}
		$comment = $this->format_comment_text( wp_kses_post( $_POST['content'] ) ); // phpcs:ignore
		$author  = $this->format_comment_text( sanitize_text_field( $_POST['name'] ) ); // phpcs:ignore
		$url     = sanitize_text_field( $_POST['url'] ); // phpcs:ignore
		$email   = sanitize_text_field( $_POST['email'] ); // phpcs:ignore
		$status  = sanitize_text_field( $_POST['status'] ); // phpcs:ignore

		$sce_comment                         = get_comment( $comment_id, ARRAY_A );
		$sce_comment['comment_content']      = $comment;
		$sce_comment['comment_author']       = $author;
		$sce_comment['comment_author_email'] = $email;
		$sce_comment['comment_author_url']   = $url;
		$sce_comment['comment_approved']     = $status;

		wp_update_comment( $sce_comment );
		die( '' );
	}

	/**
	 * Save a comment via Ajax.
	 */
	public function ajax_delete_frontend_comment() {
		$nonce = sanitize_text_field( $_POST['nonce'] ); // phpcs:ignore
		$comment_id = absint( $_POST['comment_id'] ); // phpcs:ignore
		if ( ! current_user_can( 'moderate_comments' ) ) {
			die( 'The user must be able to edit comments.' );
		}
		if ( ! wp_verify_nonce( $nonce, 'sce-modify-comment-' . $comment_id ) ) {
			die( 'Could not validate nonce.' );
		}

		wp_delete_comment( $comment_id );
		die( '' );
	}

	/**
	 * Add Fancybox scripts and styles.
	 */
	public function add_scripts_and_styles() {
		if ( ! is_singular() ) {
			return;
		}
		wp_enqueue_script( 'fancybox', plugins_url( '/fancybox/jquery.fancybox.min.js', dirname( __FILE__ ) ), array( 'jquery' ), SCE_OPTIONS_VERSION, true );
		wp_enqueue_style( 'fancybox', plugins_url( '/fancybox/jquery.fancybox.min.css', dirname( __FILE__ ) ), array(), SCE_OPTIONS_VERSION, 'all' );
	}

	/**
	 * Add editing interface for front-end comment editing.
	 *
	 * @param string $comment_content The comment content.
	 * @param object $passed_comment  A passed comment object.
	 *
	 * @return string The comment wrapper.
	 */
	public function add_edit_interface( $comment_content, $passed_comment = false ) {
		if ( ! current_user_can( 'moderate_comments' ) ) {
			return $comment_content;
		}

		// Get current comment.
		global $comment; // For Thesis.
		if ( ( ! $comment && ! $passed_comment ) || empty( $comment_content ) ) {
			return $comment_content;
		}
		if ( $passed_comment ) {
			$comment = (object) $passed_comment; // phpcs:ignore
		}

		$comment_id = absint( $comment->comment_ID );
		$edit_text  = apply_filters( 'sce_frontend_text_edit', __( 'Click to inline edit', 'simple-comment-editing-options' ) );

		// Build link.
		$link = add_query_arg(
			array(
				'comment_id' => $comment_id,
				'nonce'      => wp_create_nonce( 'sce-moderator-edit-' . $comment_id ),
			),
			admin_url( 'admin-ajax.php' )
		);

		// Return.
		$comment_wrapper = sprintf(
			'<div id="sce-front-end-comment-%d" class="sce-front-end-comment" data-cid="%d">%s</div>',
			$comment_id,
			$comment_id,
			$comment_content
		);
		return $comment_wrapper;
	}

	/**
	 * Sets up Ajax calls.
	 *
	 * Sets up Ajax calls.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function setup_ajax_calls() {
		add_action( 'wp_ajax_sce_get_moderation_comment', array( $this, 'ajax_get_comment' ) );
	}

	/**
	 * Gets a comment.
	 *
	 * Gets a comment.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function ajax_get_comment() {
		$nonce      = sanitize_text_field( $_POST['nonce'] ); // phpcs:ignore
		$comment_id = absint( $_POST['comment_id'] ); // phpcs:ignore

		// Do a permissions check.
		if ( ! current_user_can( 'moderate_comments' ) ) {
			$return = array(
				'errors' => true,
			);
			wp_send_json( $return );
			exit;
		}

		// Verify nonce.
		if ( ! wp_verify_nonce( $nonce, 'sce-moderator-edit-' . $comment_id ) ) {
			$return = array(
				'errors' => true,
			);
			wp_send_json( $return );
			exit;
		}

		$comment                  = get_comment( $comment_id );
		$comment->comment_content = $this->format_comment_text( $comment->comment_content );
		$comment->comment_author  = $this->format_comment_text( $comment->comment_author );
		wp_send_json( $comment );
		exit;
	}

	/**
	 * Returns formatted text for output.
	 *
	 * Returns formatted text for output.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param string $content The comment content.
	 *
	 * @return string formatted comment.
	 */
	private function format_comment_text( $content ) {
		// Format the comment for returning.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$content = mb_convert_encoding( $content, '' . get_option( 'blog_charset' ) . '', mb_detect_encoding( $content, 'UTF-8, ISO-8859-1, ISO-8859-15', true ) );
		}
		return $content;
	}

	/**
	 * Modify the edit link HTML
	 *
	 * @param string $link The link HTML.
	 * @param int    $comment_id The comment ID.
	 * @param string $text       The edit text.
	 *
	 * @return string modified edit link.
	 */
	public function modify_edit_link( $link, $comment_id, $text ) {
		$nonce = wp_create_nonce( 'edit-comment-' . $comment_id );
		$url   = add_query_arg(
			array(
				'action' => 'sce_options_get_frontend_comment',
				'nonce'  => $nonce,
				'cid'    => $comment_id,
			),
			admin_url( 'admin-ajax.php' )
		);
		$html  = sprintf(
			'<a data-fancybox data-type="iframe" data-src="%s" href="javascript:;">%s</a>',
			esc_url( $url ),
			__( 'Edit', 'simple-comment-editing-options' )
		);
		return $html;
	}
}
