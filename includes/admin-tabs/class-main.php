<?php
/**
 * Output main SCE tab.
 *
 * @package SCEOptions
 */

namespace SCEOptions\Includes\Admin_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access.' );
}

use SCEOptions\Includes\Options as Options;
use SCEOptions\Includes\Functions as Functions;

/**
 * Output the main tab and content.
 */
class Main {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'sceo_admin_tabs', array( $this, 'add_main_tab' ), 1, 1 );
		add_filter( 'sceo_admin_sub_tabs', array( $this, 'add_main_main_sub_tab' ), 1, 3 );
		add_filter( 'sceo_output_main', array( $this, 'output_main_content' ), 1, 3 );
	}

	/**
	 * Add the main tab and callback actions.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array of tabs.
	 */
	public function add_main_tab( $tabs ) {
		$tabs[] = array(
			'get'    => 'main',
			'action' => 'sceo_output_main',
			'url'    => Functions::get_settings_url( 'main' ),
			'label'  => _x( 'Main', 'Tab label as Main', 'ultimate-auto-updates' ),
			'icon'   => 'home-heart',
		);
		return $tabs;
	}

	/**
	 * Add the main main tab and callback actions.
	 *
	 * @param array  $tabs        Array of tabs.
	 * @param string $current_tab The current tab selected.
	 * @param string $sub_tab     The current sub-tab selected.
	 *
	 * @return array of tabs.
	 */
	public function add_main_main_sub_tab( $tabs, $current_tab, $sub_tab ) {
		if ( ( ! empty( $current_tab ) || ! empty( $sub_tab ) ) && 'main' !== $current_tab ) {
			return $tabs;
		}
		return $tabs;
	}

	/**
	 * Begin Main routing for the various outputs.
	 *
	 * @param string $tab     Main tab.
	 * @param string $sub_tab Sub tab.
	 */
	public function output_main_content( $tab, $sub_tab = '' ) {
		if ( 'main' === $tab ) {
			if ( empty( $sub_tab ) || 'main' === $sub_tab ) {
				$license_message = '';
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_sce_options' );
					Options::update_options( $_POST['options'] ); // phpcs:ignore
					printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'simple-comment-editing-options' ) );

					// Check for valid license.
					$store_url  = 'https://mediaron.com';
					$api_params = array(
						'edd_action' => 'activate_license',
						'license'    => sanitize_text_field( $_POST['options']['license'] ), // phpcs:ignore
						'item_name'  => urlencode( 'Simple Comment Editing Options' ), // phpcs:ignore
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

					// make sure the response came back okay.
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
										/* translators: %s is a date of license expiration */
										__( 'Your license key expired on %s.', 'simple-comment-editing-options' ),
										date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) // phpcs:ignore
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
									/* Translators: %s is the plugin name. */
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
				$options = Options::get_options();
				?>
				<div class="wrap">
					<form action="" method="POST">
						<?php wp_nonce_field( 'save_sce_options' ); ?>
						<h2><?php esc_html_e( 'Simple Comment Editing', 'simple-comment-editing-options' ); ?></h2>
						<p><?php esc_html_e( 'Welcome to Simple Commment Editing! You can now edit the Simple Comment Editing Options to your satisfaction.', 'simple-comment-editing-options' ); ?></p>
						<?php
						$version = get_site_option( 'sce_table_version', '0' );
						if ( SCE_OPTIONS_TABLE_VERSION === $version && true === $options['allow_comment_logging'] ) {
							global $wpdb;
							$tablename = $wpdb->base_prefix . 'sce_comments';
							$blog_id   = get_current_blog_id();

							$edit_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $tablename WHERE blog_id = %d", $blog_id ) ); // phpcs:ignore
							?>
							<p><?php esc_html_e( 'Your users have edited their comments', 'simple-comment-editing-options' ); ?> <?php echo number_format( $edit_count ); ?> <?php echo esc_html( _n( 'time', 'times', $edit_count, 'simple-comment-editing-options' ) ); ?>.</p>
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
									<th scope="row"><label for="sce-allow-front-end-editing"><?php esc_html_e( 'Enable Front End Editing for Admins', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[allow_front_end_editing]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-allow-front-end-editing" type="checkbox" <?php checked( true, $options['allow_front_end_editing'] ); ?> name="options[allow_front_end_editing]" />
													<label for="sce-allow-front-end-editing"></label>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sce-unlimited-timer"><?php esc_html_e( 'Unlimited Timer for Logged-in Users', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[allow_unlimited_editing]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-unlimited-timer" type="checkbox" <?php checked( true, $options['allow_unlimited_editing'] ); ?> name="options[allow_unlimited_editing]" />
													<label for="sce-unlimited-timer"></label>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sce-show-timer"><?php esc_html_e( 'Show Timer', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[show_timer]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-show-timer" type="checkbox" <?php checked( true, $options['show_timer'] ); ?> name="options[show_timer]" />
													<label for="sce-show-timer"></label>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sce-show-stop-timer"><?php esc_html_e( 'Allow Timer To Be Canceled by Users', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[show_stop_timer]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-show-stop-timer" type="checkbox" <?php checked( true, $options['show_stop_timer'] ); ?>name="options[show_stop_timer]"  />
													<label for="sce-show-stop-timer"></label>
												</div>
											</div>
										</div>
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
									<th scope="row"><label for="sce-allow-deletion"><?php esc_html_e( 'Allow Comment Deletion for Users', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[allow_delete]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-allow-deletion" type="checkbox" <?php checked( true, $options['allow_delete'] ); ?> name="options[allow_delete]" />
													<label for="sce-allow-deletion"></label>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sce-allow-deletion-only"><?php esc_html_e( 'Allow Comment Deletion Only', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[delete_only]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-allow-deletion-only" type="checkbox" <?php checked( true, $options['delete_only'] ); ?> name="options[delete_only]" />
													<label for="sce-allow-deletion-only"></label>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="sce-allow-deletion-confirmation"><?php esc_html_e( 'Allow Comment Deletion Confirmation', 'simple-comment-editing-options' ); ?></label></th>
									<td>
										<input type="hidden" value="false" name="options[allow_delete_confirmation]" />
										<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="sce-allow-deletion-confirmation" type="checkbox" <?php checked( true, $options['allow_delete_confirmation'] ); ?> name="options[allow_delete_confirmation]" />
													<label for="sce-allow-deletion-confirmation"></label>
												</div>
											</div>
										</div>
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
									<th scope="row"><label for="allow-comment-logging"><?php esc_html_e( 'Allow Comment Logging and Stats', 'simple-comment-editing-options' ); ?></label></th>
									<td>
									<input type="hidden" value="false" name="options[allow_comment_logging]" />
									<div class="toggle-checkboxes">
											<div class="flex">
												<div class="toggle-container">
													<input id="allow-comment-logging" type="checkbox" <?php checked( true, $options['allow_comment_logging'] ); ?> name="options[allow_comment_logging]" />
													<label for="allow-comment-logging"></label>
												</div>
											</div>
										</div>
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
	}
}
