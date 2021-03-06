<?php
/**
 * Register SCE Options and defaults, and filters/actions.
 *
 * @package SCEOptions
 */

namespace SCEOptions\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}

use SCEOptions\Includes\Options as Options;
use SCEOptions\Includes\Functions as Functions;

/**
 * Output SCE's interface and hooks.
 */
class Output {

	/**
	 * Holds options for SCE Options
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array $options
	 */
	public $options = array();

	/**
	 * Main class constructor.
	 */
	public function __construct() {

		$options = Options::get_options();
		if ( is_array( $options ) ) {
			$this->options = $options;
		}

		$this->init_filters();
		$this->init_actions();
	}

	/**
	 * Initializes SCE's various filters.
	 *
	 * Initializes SCE's various filters.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_filters() {
		add_filter( 'sce_wrapper_class', array( $this, 'output_theme_class' ) );
		add_filter( 'sce_comment_time', array( $this, 'modify_timer' ) );
		add_filter( 'sce_show_timer', array( $this, 'show_timer' ) );
		add_filter( 'sce_text_save', array( $this, 'save_button_text' ) );
		add_filter( 'sce_text_cancel', array( $this, 'save_cancel_text' ) );
		add_filter( 'sce_text_delete', array( $this, 'save_delete_text' ) );
		add_filter( 'sce_text_edit', array( $this, 'edit_text' ) );
		add_filter( 'sce_allow_delete_confirmation', array( $this, 'allow_delete_confirmation' ) );
		add_filter( 'sce_allow_delete', array( $this, 'allow_deletion' ) );
		add_filter( 'sce_loading_img', array( $this, 'loading_img' ) );
		add_filter( 'sce_confirm_delete', array( $this, 'message_confirm_delete' ) );
		add_filter( 'sce_comment_deleted', array( $this, 'message_comment_deleted' ) );
		add_filter( 'sce_comment_deleted_error', array( $this, 'message_comment_deleted_error' ) );
		add_filter( 'sce_empty_comment', array( $this, 'message_empty_comment' ) );
		add_filter( 'sce_comment_check_errors', array( $this, 'check_comment_length' ), 10, 2 );
		add_filter( 'sce_comment_check_errors', array( $this, 'check_comment_length_max' ), 10, 2 );
		add_filter( 'sce_button_extra_save', array( $this, 'maybe_add_save_icon' ) );
		add_filter( 'sce_button_extra_cancel', array( $this, 'maybe_add_cancel_icon' ) );
		add_filter( 'sce_button_extra_delete', array( $this, 'maybe_add_delete_icon' ) );
		add_filter( 'sce_button_extra_stop_timer', array( $this, 'maybe_add_stop_timer_icon' ) );
		add_filter( 'sce_unlimited_editing', array( $this, 'maybe_unlimited_editing' ), 10, 2 );
		add_filter( 'sce_allow_delete_button', array( $this, 'allow_deletion_only' ), 10, 1 );
	}

	/**
	 * Initializes SCE's various actions.
	 *
	 * Initializes SCE's various actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function init_actions() {
		add_action( 'sce_load_assets', array( $this, 'output_styles' ) );
		add_action( 'sce_save_after', array( $this, 'maybe_send_edit_email' ), 10, 4 );
		add_action( 'sce_save_after', array( $this, 'maybe_store_comment' ), 10, 4 );
		add_action( 'sce_comment_is_deleted', array( $this, 'maybe_send_delete_email' ), 10, 2 );
		add_action( 'add_meta_boxes_comment', array( $this, 'maybe_add_comment_metabox' ), 10, 1 );
	}

	/**
	 * Maybe allow for unlimited editing.
	 *
	 * Maybe allow for unlimited editing.
	 *
	 * @since 1.0.6
	 * @access public
	 *
	 * @param bool   $unlimited Whether to allow unlimited comment editing.
	 * @param object $comment comment object.
	 *
	 * @return bool Whether to allow unlimited editing
	 */
	public function maybe_unlimited_editing( $unlimited, $comment ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		if ( ! isset( $this->options['allow_unlimited_editing'] ) ) {
			return false;
		}

		if ( isset( $this->options['allow_unlimited_editing'] ) && true === $this->options['allow_unlimited_editing'] ) {
			global $current_user;
			$user_id = absint( $current_user->ID );
			if ( absint( $comment->user_id ) === $user_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Init a comment meta box.
	 *
	 * Init a comment meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $comment object.
	 *
	 * @return void
	 */
	public function maybe_add_comment_metabox( $comment ) {
		if ( isset( $this->options['allow_comment_logging'] ) && true === $this->options['allow_comment_logging'] ) {
			add_meta_box( 'sce_comment_history', __( 'Comment Edit History', 'simple-comment-editing-object' ), array( $this, 'comment_history_meta_box' ), 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Add a comment meta box.
	 *
	 * Add a comment meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $comment object.
	 *
	 * @return void
	 */
	public function comment_history_meta_box( $comment ) {
		global $wpdb;
		$tablename  = $wpdb->base_prefix . 'sce_comments';
		$blog_id    = get_current_blog_id();
		$comment_id = absint( $comment->comment_ID );

		// Get comments.
		$query   = "select * from $tablename where comment_id = %d and blog_id = %d order by date DESC";
		$query   = $wpdb->prepare( $query, $comment_id, $blog_id ); // phpcs:ignore
		$results = $wpdb->get_results( $query ); // phpcs:ignore
		if ( empty( $results ) ) {
			echo sprintf( '<p>%s</p>', esc_html__( 'No edits have occurred on this comment.', 'simple-comment-editing-options' ) );
		}
		wp_enqueue_script( 'sce-options-restore', plugins_url( '/js/simple-comment-editing-options-restore.js', dirname( __FILE__ ) ), array( 'jquery' ), SCE_OPTIONS_VERSION, true );

		// Display Comments.
		?>
		<table class="form-table sce-form-table">
			<tbody>
				<?php
				foreach ( $results as $result ) :
					?>
				<tr>
					<th scope="row">
						<?php
						$date  = $result->date;
						$datef = __( 'M j, Y @ H:i' );
						$date  = strtotime( $date );
						echo esc_html( date( $datef, $date ) ); // phpcs:ignore
						?>
					</th>
					<td>
						<?php
						$comment_text = apply_filters( 'comment_text', apply_filters( 'get_comment_text', $result->comment_content ) );
						echo wp_kses_post( $comment_text );
						?>
					</td>
					<td><a class="sce-restore-comment" href="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" data-comment-id="<?php echo absint( $comment_id ); ?>" data-id="<?php echo absint( $result->id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'restore-comment-' . absint( $comment_id ) ) ); ?>"><?php esc_html_e( 'Restore this comment', 'simple-comment-editing-options' ); ?></td>
				</tr>
					<?php
				endforeach;
				?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Add a stop timer icon.
	 *
	 * Add a stop timer icon.
	 *
	 * @since 1.0.4
	 * @access public
	 *
	 * @param string $text Button text.
	 *
	 * @return string Button text
	 */
	public function maybe_add_stop_timer_icon( $text ) {
		if ( isset( $this->options['show_icons'] ) && true === $this->options['show_icons'] ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path d="M19.04 4.55l-1.42 1.42C16.07 4.74 14.12 4 12 4c-1.83 0-3.53.55-4.95 1.48l1.46 1.46C9.53 6.35 10.73 6 12 6c3.87 0 7 3.13 7 7 0 1.27-.35 2.47-.94 3.49l1.45 1.45C20.45 16.53 21 14.83 21 13c0-2.12-.74-4.07-1.97-5.61l1.42-1.42-1.41-1.42zM15 1H9v2h6V1zm-4 8.44l2 2V8h-2v1.44zM3.02 4L1.75 5.27 4.5 8.03C3.55 9.45 3 11.16 3 13c0 4.97 4.02 9 9 9 1.84 0 3.55-.55 4.98-1.5l2.5 2.5 1.27-1.27-7.71-7.71L3.02 4zM12 20c-3.87 0-7-3.13-7-7 0-1.28.35-2.48.95-3.52l9.56 9.56c-1.03.61-2.23.96-3.51.96z"/></svg>';
		}
		return $text;
	}

	/**
	 * Add a delete icon.
	 *
	 * Add a delete icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $text Button text.
	 *
	 * @return string Button text
	 */
	public function maybe_add_delete_icon( $text ) {
		if ( isset( $this->options['show_icons'] ) && true === $this->options['show_icons'] ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';
		}
		return $text;
	}

	/**
	 * Add a cancel icon.
	 *
	 * Add a cancel icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $text Button text.
	 *
	 * @return string Button text
	 */
	public function maybe_add_cancel_icon( $text ) {
		if ( isset( $this->options['show_icons'] ) && true === $this->options['show_icons'] ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 20"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';
		}
		return $text;
	}

	/**
	 * Add a save icon.
	 *
	 * Add a save icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $text Button text.
	 *
	 * @return string Button text
	 */
	public function maybe_add_save_icon( $text ) {
		if ( isset( $this->options['show_icons'] ) && true === $this->options['show_icons'] ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path d="M0 0h24v24H0z" fill="none"/><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>';
		}
		return $text;
	}

	/**
	 * Email admin that a comment has been deleted.
	 *
	 * Email admin that a comment has been deleted.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id     Post ID.
	 * @param int $comment_id  Comment ID.
	 */
	public function maybe_send_delete_email( $post_id, $comment_id ) {
		if ( isset( $this->options['allow_edit_notification'] ) && true === $this->options['allow_edit_notification'] ) {
			$to   = $this->options['edit_notification_to'];
			$from = $this->options['edit_notification_from'];

			// Check email.
			if ( ! is_email( $to ) || ! is_email( $from ) ) {
				return;
			}

			// Get site name.
			$sitename = '';
			if ( Functions::is_multisite() ) {
				$sitename = get_site_option( 'site_name' );
			} else {
				$sitename = get_option( 'blogname' );
			}

			/* Translators: %s is the site name */
			$subject = sprintf( __( 'A user has deleted a comment from %s', 'simple-comment-editing-options' ), $sitename );

			// Get comment.
			$comment = get_comment( $comment_id, ARRAY_A );

			// Set headers.
			$headers   = array();
			$headers[] = sprintf( 'From: %s <%s>', esc_html( $sitename ), $from );

			// Get comment message.
			$message  = __( 'A user has deleted a comment on your site.', 'simple-comment-editing-options' ) . "\r\n\r\n";
			$message .= __( 'The original comment is:', 'simple-comment-editing-options' ) . "\r\n";
			$message .= $comment['comment_content'] . "\r\n\r\n";

			// Get comment trash URL.
			$comment_trash_url = esc_url_raw( add_query_arg( array( 'comment_status' => 'trash' ), admin_url( 'edit-comments.php' ) ) );
			$message          .= __( 'To permanently delete or restore this comment, follow this link:', 'simple-comment-editing-options' ) . ' ' . $comment_trash_url;

			// Send email.
			wp_mail( $to, $subject, $message, $headers );
		}
	}

	/**
	 * Store comment history.
	 *
	 * Store comment history.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $saved_comment    The saved comment.
	 * @param int   $post_id          The post ID.
	 * @param int   $comment_id       The comment ID.
	 * @param array $original_comment The original comment.
	 */
	public function maybe_store_comment( $saved_comment, $post_id, $comment_id, $original_comment ) {
		if ( isset( $this->options['allow_comment_logging'] ) && true === $this->options['allow_comment_logging'] ) {
			global $wpdb;
			$tablename                  = $wpdb->base_prefix . 'sce_comments';
			$blog_id                    = get_current_blog_id();
			$time                       = current_time( 'mysql' );
			$original_comment           = wp_filter_comment( $original_comment );
			$comment                    = wp_unslash( $original_comment );
			$comment['comment_content'] = apply_filters( 'comment_save_pre', $comment['comment_content'] );

			// Save comment data.
			$wpdb->insert( // phpcs:ignore
				$tablename,
				array(
					'blog_id'         => $blog_id,
					'comment_id'      => $comment_id,
					'comment_content' => $comment['comment_content'],
					'date'            => $time,
				),
				array(
					'%d',
					'%d',
					'%s',
					'%s',
				)
			);
		}
	}

	/**
	 * Email admin that a comment has been edited.
	 *
	 * Email admin that a comment has been edited.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $saved_comment    The saved comment.
	 * @param int   $post_id          The post ID.
	 * @param int   $comment_id       The comment ID.
	 * @param array $original_comment The original comment.
	 */
	public function maybe_send_edit_email( $saved_comment, $post_id, $comment_id, $original_comment ) {
		if ( isset( $this->options['allow_edit_notification'] ) && true === $this->options['allow_edit_notification'] ) {
			$to      = $this->options['edit_notification_to'];
			$from    = $this->options['edit_notification_from'];
			$subject = $this->options['edit_notification_subject'];

			// Check email.
			if ( ! is_email( $to ) || ! is_email( $from ) ) {
				return;
			}

			// Get site name.
			$sitename = '';
			if ( Functions::is_multisite() ) {
				$sitename = get_site_option( 'site_name' );
			} else {
				$sitename = get_option( 'blogname' );
			}

			// Set headers.
			$headers   = array();
			$headers[] = sprintf( 'From: %s <%s>', esc_html( $sitename ), $from );

			// Get comment message.
			$message  = __( 'A user has edited a comment on your site.', 'simple-comment-editing-options' ) . "\r\n\r\n";
			$message .= __( 'The original comment is:', 'simple-comment-editing-options' ) . "\r\n";
			$message .= $original_comment['comment_content'] . "\r\n\r\n";
			$message .= __( 'The edited comment is:', 'simple-comment-editing-options' ) . "\r\n";
			$message .= $saved_comment['comment_content'] . "\r\n\r\n";

			// Get comment edit URL.
			$comment_url = esc_url_raw(
				add_query_arg(
					array(
						'action' => 'editcomment',
						'c'      => $comment_id,
					),
					admin_url( 'comment.php' )
				)
			);
			$message    .= __( 'To edit or view this comment, follow this link:', 'simple-comment-editing-options' ) . ' ' . $comment_url;

			// Send email.
			wp_mail( $to, $subject, $message, $headers );
		}
	}

	/**
	 * Output styles for SCE.
	 *
	 * Output styles for SCE (front-end).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message The message to display.
	 */
	public function output_styles( $message ) {
		wp_enqueue_style( 'sce-styles', Functions::get_plugin_url( 'dist/themes.css' ), array(), SCE_OPTIONS_VERSION, 'all' );
	}

	/**
	 * Checks to see if comment meets minimum length.
	 *
	 * Checks to see if comment meets minimum length.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool  $errors Any comment errors.
	 * @param array $comment Comment to save.
	 *
	 * @return mixed false if no errors, string if errors exist
	 */
	public function check_comment_length( $errors, $comment ) {
		$allow_comment_length_check = isset( $this->options['require_comment_length'] ) ? $this->options['require_comment_length'] : $errors;
		if ( false === $allow_comment_length_check ) {
			return $errors;
		}

		// Get minimum char length.
		$minimum_char_length = isset( $this->options['min_comment_length'] ) ? $this->options['min_comment_length'] : 50;
		if ( 0 === $minimum_char_length ) {
			return $errors;
		}

		// Format comment text.
		$comment_content = trim( wp_strip_all_tags( $comment['comment_content'] ) );
		$comment_length  = strlen( $comment_content );
		if ( $comment_length < $minimum_char_length ) {
			/* Translators: %d is the minimum number of characters for a comment. */
			return sprintf( __( 'Comment must be at least %d characters.', 'simple-comment-editing-options' ), absint( $minimum_char_length ) );
		}
		return $errors;
	}

	/**
	 * Checks to see if comment meets maxiumum length.
	 *
	 * Checks to see if comment meets maxiumum length.
	 *
	 * @since 1.1.0
	 * @access public
	 *
	 * @param bool  $errors Any comment errors.
	 * @param array $comment Comment to save.
	 *
	 * @return mixed false if no errors, string if errors exist
	 */
	public function check_comment_length_max( $errors, $comment ) {
		$allow_comment_length_check = isset( $this->options['require_comment_length_max'] ) ? $this->options['require_comment_length_max'] : $errors;
		if ( false === $allow_comment_length_check ) {
			return $errors;
		}

		// Get minimum char length.
		$maximum_char_length = isset( $this->options['max_comment_length'] ) ? $this->options['max_comment_length'] : 2000;
		if ( 0 === $maximum_char_length ) {
			return $errors;
		}

		// Format comment text.
		$comment_content = trim( wp_strip_all_tags( $comment['comment_content'] ) );
		$comment_length  = strlen( $comment_content );
		if ( $comment_length > $maximum_char_length ) {
			/* Translators: %d is the maximum number of characters for a comment. */
			return sprintf( __( 'Comment cannot exceed %d characters.', 'simple-comment-editing-options' ), absint( $maximum_char_length ) );
		}
		return $errors;
	}

	/**
	 * Returns an error when a comment is empty.
	 *
	 * Returns an error when a comment is empty.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message Empty comment error.
	 * @return string New empty comment error
	 */
	public function message_empty_comment( $message ) {
		return isset( $this->options['comment_empty_error'] ) ? $this->options['comment_empty_error'] : $message;
	}

	/**
	 * Returns a delete error.
	 *
	 * Returns a delete error.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message Delete error message.
	 * @return string New delete error message
	 */
	public function message_comment_deleted_error( $message ) {
		return isset( $this->options['comment_deleted_error'] ) ? $this->options['comment_deleted_error'] : $message;
	}

	/**
	 * Returns a delete confirmation when a comment is removed.
	 *
	 * Returns a delete confirmation when a comment is removed.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message Delete removal message.
	 * @return string New delete removal message
	 */
	public function message_comment_deleted( $message ) {
		return isset( $this->options['comment_deleted'] ) ? $this->options['comment_deleted'] : $message;
	}

	/**
	 * Returns a delete confirmation modal text.
	 *
	 * Returns a delete confirmation modal text.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $message Delete confirmation message.
	 * @return string New delete confirmation message
	 */
	public function message_confirm_delete( $message ) {
		return isset( $this->options['confirm_delete'] ) ? $this->options['confirm_delete'] : $message;
	}

	/**
	 * Returns a new loading image when saving a comment.
	 *
	 * Returns a new loading image when saving a comment.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $loading_image_url Loading image url.
	 * @return string New loading image url
	 */
	public function loading_img( $loading_image_url ) {
		$loading_image = isset( $this->options['loading_image'] ) ? $this->options['loading_image'] : $loading_image_url;
		return $loading_image;
	}

	/**
	 * Returns whether a delete option is available.
	 *
	 * Returns whether a delete option is available.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $allow_deletion Whether to allow comment deletion.
	 * @return bool Whether to allow comment deletion
	 */
	public function allow_deletion( $allow_deletion ) {
		$allow_deletion = isset( $this->options['allow_delete'] ) ? $this->options['allow_delete'] : $allow_deletion;
		return $allow_deletion;
	}

	/**
	 * Check if delete only is enabled.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param bool $allow_delete Allow deletion only.
	 * @return bool true if allow deletion only.
	 */
	public function allow_deletion_only( $allow_delete ) {
		$allow_deletion_only = isset( $this->options['delete_only'] ) ? $this->options['delete_only'] : false;

		if ( ! $allow_deletion_only ) {
			return false;
		}

		add_filter( 'sce_allow_edit_button', '__return_false' );

		return true;
	}

	/**
	 * Returns whether a delete confirmation modal appears when deleting a comment.
	 *
	 * Returns whether a delete confirmation modal appears when deleting a comment.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $allow_delete_confirmation Whether to allow confirmation modal.
	 * @return bool Whether to allow confirmation modal
	 */
	public function allow_delete_confirmation( $allow_delete_confirmation ) {
		$allow_confirmation = isset( $this->options['allow_delete_confirmation'] ) ? $this->options['allow_delete_confirmation'] : $allow_delete_confirmation;
		return $allow_confirmation;
	}

	/**
	 * Returns the edit text.
	 *
	 * Returns the edit text.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $edit_text The main edit text for SCE.
	 * @return string New edit text
	 */
	public function edit_text( $edit_text ) {
		$new_edit_text = isset( $this->options['click_to_edit_text'] ) ? $this->options['click_to_edit_text'] : '';
		if ( '' === $new_edit_text ) {
			return $edit_text;
		}
		return $new_edit_text;
	}

	/**
	 * Returns button text for delete button.
	 *
	 * Returns button text for delete button.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $button_text Button text.
	 * @return string New button text
	 */
	public function save_delete_text( $button_text ) {
		$new_button_text = isset( $this->options['delete_text'] ) ? $this->options['delete_text'] : '';
		if ( '' === $new_button_text ) {
			return $button_text;
		}
		return $new_button_text;
	}

	/**
	 * Returns button text for cancel button.
	 *
	 * Returns button text for cancel button.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $button_text Button text.
	 * @return string New button text
	 */
	public function save_cancel_text( $button_text ) {
		$new_button_text = isset( $this->options['cancel_text'] ) ? $this->options['cancel_text'] : '';
		if ( '' === $new_button_text ) {
			return $button_text;
		}
		return $new_button_text;
	}

	/**
	 * Returns button text for save button.
	 *
	 * Returns button text for save button.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $button_text Button text.
	 * @return string New button text
	 */
	public function save_button_text( $button_text ) {
		$new_button_text = isset( $this->options['save_text'] ) ? $this->options['save_text'] : '';
		if ( '' === $new_button_text ) {
			return $button_text;
		}
		return $new_button_text;
	}

	/**
	 * Returns whether to show a timer.
	 *
	 * Returns whether to show a timer.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $show_timer Whether to show the timer or not.
	 * @return bool Whether to show the timer or not
	 */
	public function show_timer( $show_timer ) {
		$new_show_timer = isset( $this->options['show_timer'] ) ? $this->options['show_timer'] : '';
		if ( '' === $new_show_timer ) {
			return $show_timer;
		}
		return $new_show_timer;
	}

	/**
	 * Returns a new timer.
	 *
	 * Returns a new timer.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $timer Time in minutes to edit the comment.
	 * @return int New time in minutes
	 */
	public function modify_timer( $timer ) {
		$new_timer = isset( $this->options['timer'] ) ? $this->options['timer'] : false;
		if ( false === $new_timer ) {
			return $timer;
		}
		return $new_timer;
	}

	/**
	 * Returns a theme class.
	 *
	 * Returns a theme class.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $classes SCE Wrapper class.
	 * @return array $classes New SCE theme classes
	 */
	public function output_theme_class( $classes = array() ) {
		$theme = isset( $this->options['button_theme'] ) ? $this->options['button_theme'] : false;
		if ( false === $theme ) {
			return $classes;
		}
		$classes[] = $theme;
		return $classes;
	}

}
