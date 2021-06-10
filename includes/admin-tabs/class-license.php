<?php
/**
 * Output licenses tab.
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
class License {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'sceo_admin_tabs', array( $this, 'add_main_tab' ), 1, 1 );
		add_filter( 'sceo_admin_sub_tabs', array( $this, 'add_main_main_sub_tab' ), 1, 3 );
		add_filter( 'sceo_output_license', array( $this, 'output_main_content' ), 1, 3 );
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
			'get'    => 'license',
			'action' => 'sceo_output_license',
			'url'    => Functions::get_settings_url( 'license' ),
			'label'  => _x( 'License', 'Tab label as License', 'ultimate-auto-updates' ),
			'icon'   => 'shield-check',
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
		if ( 'license' === $tab ) {
			if ( empty( $sub_tab ) || 'license' === $sub_tab ) {
				$license_message = '';
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_sce_options' );
					Options::update_options( $_POST['options'] ); // phpcs:ignore

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
				<div class="sce-admin-panel-area">
					<div class="sce-panel-row">
						<h2><?php esc_html_e( 'Please enter your license below', 'simple-comment-editing-options' ); ?></h2>
						<p class="description"><?php esc_html_e( 'A license will ensure you are alerted to any updates.', 'simple-comment-editing-options' ); ?></p>
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
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
