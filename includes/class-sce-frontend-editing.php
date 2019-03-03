<?php
if (!defined('ABSPATH')) die('No direct access.');
class SCE_Frontend_Editing {

	public function __construct() {
		add_action( 'init', array( $this, 'setup_ajax_calls' ) );
		add_action( 'wp_footer', array( $this, 'add_editing_interface_footer' ) );
		add_action( 'init', array( $this, 'setup_comment_filters' ) );
	}

	public function setup_comment_filters() {
		/* Begin Filters */
		if ( !is_feed() && !defined( 'DOING_SCE' ) ) {
			add_filter( 'comment_excerpt', array( $this, 'add_edit_interface'), 1000, 2 );
			add_filter( 'comment_text', array( $this, 'add_edit_interface'), 1000, 2 );
			add_filter( 'thesis_comment_text', array( $this, 'add_edit_interface'), 1000, 2 );
		}
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

	public function add_edit_interface( $comment_content, $passed_comment = false ) {
		if( ! current_user_can( 'moderate_comments' ) ) return $comment_content;

		// Get current comment
		global $comment; // For Thesis
		if ( ( ! $comment && ! $passed_comment ) || empty( $comment_content ) ) return $comment_content;
		if ( $passed_comment ) {
			$comment = (object)$passed_comment;
		}

		$comment_id = absint( $comment->comment_ID );
		$edit_text = apply_filters( 'sce_text_edit', __( 'Click to edit', 'simple-comment-editing-options' ) );

		// Build link
		$link = add_query_arg( array(
			'comment_id' => $comment_id,
			'nonce'      => wp_create_nonce( 'sce-moderator-edit-' . $comment_id ),
		),
		admin_url( 'admin-ajax.php' ) );

		$html = '';
		$html .= '<div class="sce-moderator-edit-link">';
		$html .= sprintf( '<a href="%s" onclick="sce_get_comment(event)">%s</a>', esc_url( $link ), esc_html( $edit_text ) );
		$html .= '</div>';

		// Return
		$comment_wrapper = sprintf( '<div id="sce-comment%d" class="sce-comment">%s</div>', $comment_id, $comment_content );
		$comment_wrapper .= $html;
		return $comment_wrapper;
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
		$nonce = $_POST['nonce'];
		$comment_id = absint( $_POST['comment_id'] );

		// Do a permissions check
		if ( ! current_user_can( 'moderate_comments' ) ) {
			$return = array(
				'errors' => true
			);
			wp_send_json( $return );
			exit;
		}

		// Verify nonce
		if ( ! wp_verify_nonce( $nonce, 'sce-moderator-edit-' . $comment_id ) ) {
			$return = array(
				'errors' => true
			);
			wp_send_json( $return );
			exit;
		}

		$comment = get_comment( $comment_id );

		wp_send_json( $comment );
		exit;
	}

	/**
	 * Adds editing interface to footer.
	 *
	 * Adds editing interface to footer.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_editing_interface_footer() {
		if ( !is_single() && !is_singular() && !is_page() ) return;
		if( ! current_user_can( 'moderate_comments' ) ) return;
		ob_start();
		?>
<div class="sce-inline-interface-hidden">
	<div class="sce-inline-name-wrapper">
		<label><span class="sce-inline-input"><?php esc_html_e( 'Name', 'simple-comment-editing-options' ); ?></span><input type="text" class="sce-inline-name" /></label>
	</div>
	<div class="sce-inline-email-wrapper">
		<label><span class="sce-inline-input"><?php esc_html_e( 'Email', 'simple-comment-editing-options' ); ?></span><input type="text" class="sce-inline-email" /></label>
	</div>
	<div class="sce-inline-url-wrapper">
		<label><span class="sce-inline-input"><?php esc_html_e( 'URL', 'simple-comment-editing-options' ); ?></span><input type="text" class="sce-inline-url" /></label>
	</div>
	<div class="sce-inline-comment-wrapper">
		<textarea class="sce-inline-comment"></textarea>
	</div>
	<div class="sce-inline-status-wrapper">
		<label><input type="radio" value="approved" name="comment_status" /> <?php esc_html_e( 'Approved', 'simple-comment-editing-options' ); ?></label><br />
		<label><input type="radio" value="pending" name="comment_status" /> <?php esc_html_e( 'Pending', 'simple-comment-editing-options' ); ?></label><br />
		<label><input type="radio" value="spam" name="comment_status" /> <?php esc_html_e( 'Spam', 'simple-comment-editing-options' ); ?></label>
	</div>
</div>
		<?php
		echo ob_get_clean();
	}


}