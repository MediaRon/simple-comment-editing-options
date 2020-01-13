<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}
class SCE_Admin_Menu_Output {

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	} //end get_instance

	public function __construct() {
		if ( is_admin() ) {
			$this->output_options();
		}
	}

	/**
	 * Output options
	 *
	 * @since 1.0.0
	 * @access public
	 * @see __construct
	 */
	public function output_options() {

		$license_message = '';
		if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
			check_admin_referer( 'save_sce_options' );
			include_once SCE_Options::get_instance()->get_plugin_dir( 'includes/class-sce-options.php' );
			$sce_options = new SCE_Plugin_Options();
			$sce_options->update_options( $_POST['options'] );
			printf( '<div class="updated"><p><strong>%s</strong></p></div>', __( 'Your options have been saved.', 'simple-comment-editing-options' ) );

			// Check for valid license
			$store_url  = 'https://mediaron.com';
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $_POST['options']['license'],
				'item_name'  => urlencode( 'Simple Comment Editing Options' ),
				'url'        => home_url(),
			);
			// Call the custom API.
			$response = wp_remote_post(
				$store_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$license_message = $response->get_error_message();
				} else {
					$license_message = __( 'An error occurred, please try again.', 'simple-comment-editing-options' );
				}
			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {
					delete_site_option( 'sce_license_status' );
					switch ( $license_data->error ) {

						case 'expired':
							$license_message = sprintf(
								__( 'Your license key expired on %s.', 'simple-comment-editing-options' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'disabled':
						case 'revoked':
							$license_message = __( 'Your license key has been disabled.', 'simple-comment-editing-options' );
							break;

						case 'missing':
							$license_message = __( 'Invalid license.', 'simple-comment-editing-options' );
							break;

						case 'invalid':
						case 'site_inactive':
							$license_message = __( 'Your license is not active for this URL.', 'simple-comment-editing-options' );
							break;

						case 'item_name_mismatch':
							$license_message = sprintf( __( 'This appears to be an invalid license key for %s.', 'simple-comment-editing-options' ), 'Simple Comment Editing Options' );
							break;

						case 'no_activations_left':
							$license_message = __( 'Your license key has reached its activation limit.', 'simple-comment-editing-options' );
							break;

						default:
							$license_message = __( 'An error occurred, please try again.', 'simple-comment-editing-options' );
							break;
					}
				}
				if ( empty( $license_message ) ) {
					update_site_option( 'sce_license_status', $license_data->license );
				}
			}
		}
		// Get options and defaults.
		include_once SCE_Options::get_instance()->get_plugin_dir( 'includes/class-sce-options.php' );
		$sce_options = new SCE_Plugin_Options();
		$options = $sce_options->get_options();
		?>
		<div class="wrap">
			<form action="" method="POST">
				<?php wp_nonce_field( 'save_sce_options' ); ?>
				<h2><?php esc_html_e( 'Simple Comment Editing', 'simple-comment-editing-options' ); ?></h2>
				<p><?php esc_html_e( 'Welcome to Simple Commment Editing! You can now edit the Simple Comment Editing Options to your satisfaction.', 'simple-comment-editing-options' ); ?></p>
				<?php
				$version = get_site_option( 'sce_table_version', '0' );
				if ( $version === SCE_OPTIONS_TABLE_VERSION && true === $options['allow_comment_logging'] ) {
					global $wpdb;
					$tablename = $wpdb->base_prefix . 'sce_comments';
					$blog_id   = get_current_blog_id();

					$edit_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $tablename WHERE blog_id = %d", $blog_id ) );
					?>
					<p><?php esc_html_e( 'Your users have edited their comments', 'simple-comment-editing-options' ); ?> <?php echo number_format( $edit_count ); ?> <?php echo _n( 'time', 'times', $edit_count, 'simple-comment-editing-options' ); ?>.</p>
					<?php
				}
				?>
				<table class="form-table">
					<tbody>
					<tr>
							<th scope="row"><label for="sce-license"><?php esc_html_e( 'Enter Your License', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input id="sce-license" class="regular-text" type="text" value="<?php echo esc_attr( $options['license'] ); ?>" name="options[license]" /><br />
								<?php
								$license_status = get_site_option( 'sce_license_status', false );
								if ( false === $license_status ) {
									printf( '<p>%s</p>', esc_html__( 'Please enter your licence key.', 'simple-comment-editing-options' ) );
								} else {
									printf( '<p>%s</p>', esc_html__( 'Your license is valid and you will now receive update notifications.', 'simple-comment-editing-options' ) );
								}
								?>
								<?php
								if ( ! empty( $license_message ) ) {
									printf( '<div class="updated error"><p><strong>%s</p></strong></div>', esc_html( $license_message ) );
								}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-timer"><?php esc_html_e( 'Edit Timer in Minutes', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input id="sce-timer" class="regular-text" type="number" value="<?php echo esc_attr( absint( $options['timer'] ) ); ?>" name="options[timer]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-show-timer"><?php esc_html_e( 'Unlimited Timer', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[allow_unlimited_editing]" />
								<input id="sce-unlimited-timer" type="checkbox" value="true" name="options[allow_unlimited_editing]" <?php checked( true, $options['allow_unlimited_editing'] ); ?> /> <label for="sce-unlimited-timer"><?php esc_html_e( 'Allow unlimited editing for logged in users.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-show-timer"><?php esc_html_e( 'Show Timer', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[show_timer]" />
								<input id="sce-show-timer" type="checkbox" value="true" name="options[show_timer]" <?php checked( true, $options['show_timer'] ); ?> /> <label for="sce-show-timer"><?php esc_html_e( 'Show timer (We recommend hiding the timer if you have a high timer).', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-show-timer"><?php esc_html_e( 'Allow Timer To Be Canceled', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<input type="hidden" value="false" name="options[show_stop_timer]" />
								<input id="sce-show-stop-timer" type="checkbox" value="true" name="options[show_stop_timer]" <?php checked( true, $options['show_stop_timer'] ); ?> /> <label for="sce-show-stop-timer"><?php esc_html_e( 'Allow timer to be stopped.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-timer-appearance"><?php esc_html_e( 'Timer Appearance', 'simple-comment-editing-options' ); ?></label></th>
							<td>
								<select name="options[timer_appearance]">
									<option value="words" <?php selected( 'words', $options['timer_appearance'] ); ?>><?php esc_html_e( 'Words', 'simple-comment-editing-options' ); ?></option>
									<option value="compact" <?php selected( 'compact', $options['timer_appearance'] ); ?>><?php esc_html_e( 'Compact', 'simple-comment-editing-options' ); ?></option>
								</select>
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
									<option value="default" <?php selected( 'default', $options['button_theme'] ); ?>><?php esc_html_e( 'None', 'simple-comment-editing-options' ); ?></option>
									<option value="regular" <?php selected( 'regular', $options['button_theme'] ); ?>><?php esc_html_e( 'Regular', 'simple-comment-editing-options' ); ?></option>
									<option value="dark" <?php selected( 'dark', $options['button_theme'] ); ?> ><?php esc_html_e( 'Dark', 'simple-comment-editing-options' ); ?></option>
									<option value="light" <?php selected( 'light', $options['button_theme'] ); ?>><?php esc_html_e( 'Light', 'simple-comment-editing-options' ); ?></option>
								</select>
								<br /><br />
								<input type="hidden" value="false" name="options[show_icons]" />
								<input id="sce-allow-icons" type="checkbox" value="true" name="options[show_icons]" <?php checked( true, $options['show_icons'] ); ?> /> <label for="sce-allow-icons"><?php esc_html_e( 'Allow icons for the buttons. Recommended if you have selected a button theme.', 'simple-comment-editing-options' ); ?></label>
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
							<br /><br />
							<label for="sce-comment-stop-timer"><?php esc_html_e( 'Stop Timer Text', 'simple-comment-editing-options' ); ?></label><br />
							<input id="sce-comment-stop-timer" class="regular-text" type="text" value="<?php echo esc_attr( $options['stop_timer_text'] ); ?>" name="options[stop_timer_text]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label></th>
							<td>
							<label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-loading-img" class="regular-text" type="text" value="<?php echo esc_attr( esc_url( $options['loading_image'] ) ); ?>" name="options[loading_image]" /><br />
								<img src="<?php echo esc_attr( $options['loading_image'] ); ?>" width="25" height="25" alt="Loading" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Messages', 'simple-comment-editing-options' ); ?></th>
							<td>
							<label for="sce-edit-text"><?php esc_html_e( 'Edit Text', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-edit-text" class="regular-text" type="text" value="<?php echo esc_attr( $options['click_to_edit_text'] ); ?>" name="options[click_to_edit_text]" />
							<br /><br />
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
							<th scope="row"><?php esc_html_e( 'Email Notifications', 'simple-comment-editing-options' ); ?></th>
							<td>
								<p><?php esc_html_e( 'Receive an email notification each time a user edits or deletes their comment.', 'simple-comment-editing-options' ); ?></p>
								<br />
								<input type="hidden" value="false" name="options[allow_edit_notification]" />
								<input id="sce-allow-email-notifications" type="checkbox" value="true" name="options[allow_edit_notification]" <?php checked( true, $options['allow_edit_notification'] ); ?> /> <label for="sce-allow-email-notifications"><?php esc_html_e( 'Allow email notifications.', 'simple-comment-editing-options' ); ?></label>
								<br /><br />
								<label for="sce-email-notifications_to"><?php esc_html_e( 'To Email Address', 'simple-comment-editing-options' ); ?></label><br />
									<input id="sce-email-notifications_to" class="regular-text" type="text" value="<?php echo esc_attr( $options['edit_notification_to'] ); ?>" name="options[edit_notification_to]" />
								<br /><br />
								<label for="sce-email-notifications-from"><?php esc_html_e( 'From Email Address', 'simple-comment-editing-options' ); ?></label><br />
									<input id="sce-email-notifications-from" class="regular-text" type="text" value="<?php echo esc_attr( $options['edit_notification_from'] ); ?>" name="options[edit_notification_from]" />
								<br /><br />
								<label for="sce-email-notifications-subject"><?php esc_html_e( 'Email Subject', 'simple-comment-editing-options' ); ?></label><br />
									<input id="sce-email-notifications-subject" class="regular-text" type="text" value="<?php echo esc_attr( $options['edit_notification_subject'] ); ?>" name="options[edit_notification_subject]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Comment Length', 'simple-comment-editing-options' ); ?></th>
							<td>
								<p><?php esc_html_e( 'Allow front-end comment character control', 'simple-comment-editing-options' ); ?></p>
								<br />
								<input type="radio" value="false" name="options[allow_front_end_character_limit]" id="disable-ccc" <?php checked( false, $options['allow_front_end_character_limit'] ); ?> /> <label for="disable-ccc"><?php esc_html_e( 'Disable Comment Character Control', 'simple-comment-editing-options' ); ?></label><br />
								<input type="radio" value="true" name="options[allow_front_end_character_limit]" id="enable-ccc" <?php checked( true, $options['allow_front_end_character_limit'] ); ?> /> <label for="enable-ccc"><?php esc_html_e( 'Enable Comment Character Control', 'simple-comment-editing-options' ); ?></label><br /><br /><br />
								<input type="hidden" value="false" name="options[require_comment_length]" />
								<input id="sce-allow-comment-length" type="checkbox" value="true" name="options[require_comment_length]" <?php checked( true, $options['require_comment_length'] ); ?> /> <label for="sce-allow-comment-length"><?php esc_html_e( 'Ensure an edited comment has a minimum length in characters.', 'simple-comment-editing-options' ); ?></label>
								<br /><br />
								<label for="sce-comment-length"><?php esc_html_e( 'Minimum Comment Length', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-length" class="regular-text" type="number" value="<?php echo esc_attr( $options['min_comment_length'] ); ?>" name="options[min_comment_length]" />
								<br /><br />
								<input id="sce-allow-comment-length-max" type="checkbox" value="true" name="options[require_comment_length_max]" <?php checked( true, $options['require_comment_length_max'] ); ?> /> <label for="sce-allow-comment-length-max"><?php esc_html_e( 'Ensure an edited comment has a maximum length in characters.', 'simple-comment-editing-options' ); ?></label>
								<br /><br />
								<label for="sce-comment-length-max"><?php esc_html_e( 'Maximum Comment Length', 'simple-comment-editing-options' ); ?></label><br />
								<input id="sce-comment-length-max" class="regular-text" type="number" value="<?php echo esc_attr( $options['max_comment_length'] ); ?>" name="options[max_comment_length]" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Allow Comment Logging and Stats', 'simple-comment-editing-options' ); ?></th>
							<td>
							<input type="hidden" value="false" name="options[allow_comment_logging]" />
								<input id="sce-allow-comment-logging" type="checkbox" value="true" name="options[allow_comment_logging]" <?php checked( true, $options['allow_comment_logging'] ); ?> /> <label for="sce-allow-comment-logging"><?php esc_html_e( 'Store edited comments in a custom table and show a history of edited comments when viewing the comment in the WordPress admin area.', 'simple-comment-editing-options' ); ?></label>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Options', 'simple-comment-editing-options' ) ); ?>
			</form>
		</div>
		<?php
	}

}
