<?php
if (!defined('ABSPATH')) die('No direct access.');
class SCE_Admin_Menu_Output {

	public function __construct() {
		$this->output_options();
	}

	/**
	 * Output options
	 *
	 * @since 1.0.0
	 * @access public
	 * @see __construct
	 */
	public function output_options() {
		
		if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
			check_admin_referer( 'save_sce_options' );
			$this->update_options( $_POST['options'] );
			printf( '<div class="updated"><p><strong>%s</strong></p></div>', __( 'Your options have been saved.', 'simple-comment-editing-options' ) );
		}
		// Get options and defaults
		$options = get_site_option( 'sce', false );
		if ( false === $options ) {
			$options = $this->get_defaults();
		} elseif( is_array( $options ) ) {
			$options = wp_parse_args( $options, $this->get_defaults() );
		} else {
			$options = $this->get_defaults();
		}
		?>
		<div class="wrap">
			<form action="" method="POST">
				<?php wp_nonce_field('save_sce_options'); ?>
				<h2><?php esc_html_e( 'Simple Comment Editing', 'simple-comment-editing-options' ); ?></h2>
				<p><?php esc_html_e( 'Welcome to Simple Commment Editing! You can now edit the Simple Comment Editing Options to your satisfaction.', 'simple-comment-editing-options' ); ?></p>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="sce-timer"><?php esc_html_e('Edit Timer in Minutes', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input id="sce-timer" class="regular-text" type="number" value="<?php echo esc_attr( absint( $options['timer'] ) ); ?>" name="options[timer]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-show-timer"><?php esc_html_e( 'Show Timer', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[show_timer]" />
								<input id="sce-show-timer" type="checkbox" value="true" name="options[show_timer]" <?php checked( true, $options['show_timer'] ); ?> /> <label for="sce-show-timer"><?php esc_html_e( 'Show Timer (We recommend hiding the timer if you have a high timer)', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-allow-deletion"><?php esc_html_e( 'Allow Comment Deletion', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[allow_delete]" />
								<input id="sce-allow-deletion" type="checkbox" value="true" name="options[allow_delete]" <?php checked( true, $options['allow_delete'] ); ?> /> <label for="sce-allow-deletion"><?php esc_html_e( 'Allow users to delete their comments.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-allow-deletion-confirmation"><?php esc_html_e( 'Allow Comment Deletion Confirmation', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[allow_delete_confirmation]" />
								<input id="sce-allow-deletion-confirmation" type="checkbox" value="true" name="options[allow_delete_confirmation]" <?php checked( true, $options['allow_delete_confirmation'] ); ?> /> <label for="sce-allow-deletion-confirmation"><?php esc_html_e( 'Allow the modal warning for comment deletion.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-button-theme"><?php esc_html_e( 'Button Theme', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<select name="options[button_theme]">
									<option value="default" <?php selected( 'default', $options['button_theme'] );?>><?php esc_html_e( 'None', 'simple-comment-editing-options' ); ?></option>
									<option value="regular" <?php selected( 'regular', $options['button_theme'] );?> <?php selected( 'regular', $options['button_theme'] );?>><?php esc_html_e( 'Regular', 'simple-comment-editing-options' ); ?></option>
									<option value="regular_icons" <?php selected( 'regular_icons', $options['button_theme'] );?> <?php selected( 'regular_icons', $options['button_theme'] );?>><?php esc_html_e( 'Regular With Icons', 'simple-comment-editing-options' ); ?></option>
									<option value="dark" <?php selected( 'dark', $options['button_theme'] );?> <?php selected( 'dark', $options['button_theme'] );?>><?php esc_html_e( 'Dark', 'simple-comment-editing-options' ); ?></option>
									<option value="dark_icons" <?php selected( 'dark_icons', $options['button_theme'] );?>><?php esc_html_e( 'Dark With Icons', 'simple-comment-editing-options' ); ?></option>
									<option value="light" <?php selected( 'light', $options['button_theme'] );?>><?php esc_html_e( 'Light', 'simple-comment-editing-options' ); ?></option>
									<option value="light_icons" <?php selected( 'light_icons', $options['button_theme'] );?>><?php esc_html_e( 'Light With Icons', 'simple-comment-editing-options' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Button Text', 'simple-comment-editing-options' ); ?></th>
							<td>
							<label for="sce-save-text"><?php esc_html_e( 'Save Button Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-save-text" class="regular-text" type="text" value="<?php echo esc_attr( $options['save_text'] ); ?>" name="options[save_text]" />
							<br /><br />
							<label for="sce-cancel-text"><?php esc_html_e( 'Cancel Button Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-cancel-text" class="regular-text" type="text" value="<?php echo esc_attr( $options['cancel_text'] ); ?>" name="options[cancel_text]" />
							<br /><br />
							<label for="sce-delete-text"><?php esc_html_e( 'Delete Button Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-delete-text" class="regular-text" type="text" value="<?php echo esc_attr( $options['delete_text'] ); ?>" name="options[delete_text]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label></th>
							<td>
							<label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-loading-img" class="regular-text" type="text" value="<?php echo esc_attr( $options['loading_image'] ); ?>" name="options[loading_image]" /><br />
								<img src="<?php echo esc_attr( $options['loading_image'] ); ?>" width="25" height="25" alt="Loading" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Messages', 'simple-comment-editing-options' ); ?></th>
							<td>
							<label for="sce-confirm-delete"><?php esc_html_e( 'Comment Deletion Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-confirm-delete" class="regular-text" type="text" value="<?php echo esc_attr( $options['confirm_delete'] ); ?>" name="options[confirm_delete]" />
							<br /><br />
							<label for="sce-comment-deleted"><?php esc_html_e( 'Comment Deleted Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-deleted" class="regular-text" type="text" value="<?php echo esc_attr( $options['comment_deleted'] ); ?>" name="options[comment_deleted]" />
							<br /><br />
							<label for="sce-comment-deleted-error"><?php esc_html_e( 'Comment Deleted Error', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-deleted-error" class="regular-text" type="text" value="<?php echo esc_attr( $options['comment_deleted_error'] ); ?>" name="options[comment_deleted_error]" />
							<br /><br />
							<label for="sce-comment-empty-error"><?php esc_html_e( 'Comment Empty Error', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-empty-error" class="regular-text" type="text" value="<?php echo esc_attr( $options['comment_empty_error'] ); ?>" name="options[comment_empty_error]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Comment Length', 'simple-comment-editing-options' ); ?></th>
							<td>
							<input type="hidden" value="false" name="options[require_comment_length]" />
								<input id="sce-allow-comment-lengthn" type="checkbox" value="true" name="options[require_comment_length]" <?php checked( true, $options['require_comment_length'] ); ?> /> <label for="sce-allow-comment-lengthn"><?php esc_html_e( 'Ensure an edited comment has a minimum length in characters.', 'simple-comment-editing-options' ); ?></label>
								<br /><br />
								<label for="sce-comment-length"><?php esc_html_e( 'Minimum Comment Length', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-length" class="regular-text" type="number" value="<?php echo esc_attr( $options['min_comment_length'] ); ?>" name="options[min_comment_length]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Allow Comment Logging and Stats', 'simple-comment-editing-options' ); ?></th>
							<td>
							<input type="hidden" value="false" name="options[allow_comment_logging]" />
								<input id="sce-allow-comment-logging" type="checkbox" value="true" name="options[allow_comment_logging]" <?php checked( true, $options['allow_comment_logging'] ); ?> /> <label for="sce-alllow-comment-logging"><?php esc_html_e( 'Store edited comments in a custom table and show a history of edited comments when viewing the comment in the WordPress admin area.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Options', 'simple-comment-editing-options' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Update options via sanitization
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $options array of options to save
	 * @return void
	 */
	private function update_options( $options ) {
		foreach( $options as $key => &$option ) {
			switch( $key ) {
				case 'timer':
					$timer = absint( $options[$key] );
					if( 0 === $timer ) {
						$timer = 5;
					}
					$option = $timer;
					break;
				case 'min_comment_length':
					$option = absint( $options[$key] );
					break;
				case 'allow_comment_logging':
				case 'require_comment_length':
				case 'require_comment_length':
				case 'allow_delete_confirmation':
				case 'allow_delete':
				case 'show_timer':
					$option = filter_var( $options[$key], FILTER_VALIDATE_BOOLEAN );
					break;
				default:
					$option = sanitize_text_field( $options[$key] );
					break;
			}
		}
		update_site_option( 'sce', $options );
	}

	/**
	 * Get defaults for SCE options
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @return array default options
	 */
	private function get_defaults() {
		$defaults = array(
			'timer'                     => 5,
			'show_timer'                => true,
			'loading_image'             => Simple_Comment_Editing::get_instance()->get_plugin_url( '/images/loading.gif' ),
			'allow_delete'              => true,
			'button_theme'              => 'default',
			'click_to_eedit_text'       => __( 'Click to Edit', 'simple-comment-editing' ),
			'show_timer'                => true,
			'save_text'                 => __( 'Save', 'simple-comment-editing' ),
			'cancel_text'               => __( 'Cancel', 'simple-comment-editing' ),
			'delete_text'               => __( 'Delete', 'simple-comment-editing' ),
			'custom_class'              => '',
			'allow_delete_confirmation' => true,
			'confirm_delete'            => __( 'Do you want to delete this comment?', 'simple-comment-editing' ),
			'comment_deleted'           => __( 'Your comment has been removed.', 'simple-comment-editing' ),
			'comment_deleted_error'     => __( 'Your comment could not be deleted', 'simple-comment-editing' ),
			'comment_empty_error'       => Simple_Comment_Editing::get_instance()->errors->get_error_message( 'comment_empty' ),
			'require_comment_length'    => false,
			'min_comment_length'        => 50,
			'allow_comment_logging'     => false,
			'table_exists'              => false
		);
		return $defaults;
	}

}