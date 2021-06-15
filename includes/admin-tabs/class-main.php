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
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_sce_options' );
					$options = Options::get_options();
					$options = wp_parse_args( $_POST['options'], $options ); // phpcs:ignore
					Options::update_options( $options );
					printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'simple-comment-editing-options' ) );
				}
				// Get options and defaults.
				$options = Options::get_options();
				?>
				<div class="sce-admin-panel-area">
					<div class="sce-panel-row">
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
							<h2><?php esc_html_e( 'Main Options', 'simple-comment-editing-options' ); ?></h2>
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
										<th scope="row"><label for="sce-timer"><?php esc_html_e( 'Edit Timer in Minutes', 'simple-comment-editing-options' ); ?></label></th>
										<td>
											<input id="sce-timer" class="regular-text" type="number" value="<?php echo esc_attr( absint( $options['timer'] ) ); ?>" name="options[timer]" />
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
											<p class="description"><?php esc_html_e( 'Enabling this will allow you to restore edited comments and view an edit history for comments.', 'simple-comment-editing-options' ); ?></p>
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
								</tbody>
							</table>

							<?php submit_button( __( 'Save Options', 'simple-comment-editing-options' ) ); ?>
						</form>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<div class="sce-panel-row">
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
							<h2><?php esc_html_e( 'Email Settings', 'simple-comment-editing-options' ); ?></h2>
							<table class="form-table">
								<tbody>
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
								</tbody>
							</table>

							<?php submit_button( __( 'Save Email Settings', 'simple-comment-editing-options' ) ); ?>
						</form>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<div class="sce-panel-row">
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
							<h2><?php esc_html_e( 'Comment Character Control', 'simple-comment-editing-options' ); ?></h2>
							<p class="description"><?php esc_html_e( 'Enabling Comment Character Control will set a minimum and maximum length for comments.', 'simple-comment-editing-options' ); ?></p>
							<table class="form-table">
								<tbody>
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
								</tbody>
							</table>

							<?php submit_button( __( 'Save Options', 'simple-comment-editing-options' ) ); ?>
						</form>
					</div>
				</div>
				<?php
			}
		}
	}
}
