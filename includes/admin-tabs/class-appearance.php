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
 * Output the appearance tab and content.
 */
class Appearance {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'sceo_admin_tabs', array( $this, 'add_main_tab' ), 1, 9 );
		add_filter( 'sceo_admin_sub_tabs', array( $this, 'add_main_main_sub_tab' ), 1, 3 );
		add_filter( 'sceo_output_appearance', array( $this, 'output_main_content' ), 1, 3 );
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
			'get'    => 'appearance',
			'action' => 'sceo_output_appearance',
			'url'    => Functions::get_settings_url( 'appearance' ),
			'label'  => _x( 'Appearance', 'Tab label as Appearance', 'ultimate-auto-updates' ),
			'icon'   => 'paintbrush',
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
		if ( 'appearance' === $tab ) {
			if ( empty( $sub_tab ) || 'appearance' === $sub_tab ) {
				$license_message = '';
				if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
					check_admin_referer( 'save_sce_options' );
					Options::update_options( $_POST['options'] ); // phpcs:ignore
				}
				// Get options and defaults.
				$options = Options::get_options();
				?>
				<div class="sce-admin-panel-area">
					<div class="sce-panel-row">
						<h2><?php esc_html_e( 'Appearance Settings', 'simple-comment-editing-options' ); ?></h2>
						<p class="description"><?php esc_html_e( 'Change the appearance of Simple Comment Editing with the options below.', 'simple-comment-editing-options' ); ?></p>
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
							<table class="form-table">
								<tbody>
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
										<th scope="row"><label for="sce-timer-appearance"><?php esc_html_e( 'Timer Appearance', 'simple-comment-editing-options' ); ?></label></th>
										<td>
											<select name="options[timer_appearance]">
												<option value="words" <?php selected( 'words', $options['timer_appearance'] ); ?>><?php esc_html_e( 'Words', 'simple-comment-editing-options' ); ?></option>
												<option value="compact" <?php selected( 'compact', $options['timer_appearance'] ); ?>><?php esc_html_e( 'Compact', 'simple-comment-editing-options' ); ?></option>
											</select>
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
										<th scope="row"><label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label></th>
										<td>
										<label for="sce-loading-img"><?php esc_html_e( 'Loading Image', 'simple-comment-editing-options' ); ?></label><br />
											<input id="sce-loading-img" class="regular-text" type="text" value="<?php echo esc_attr( esc_url( $options['loading_image'] ) ); ?>" name="options[loading_image]" /><br />
											<img src="<?php echo esc_attr( $options['loading_image'] ); ?>" width="25" height="25" alt="Loading" />
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
