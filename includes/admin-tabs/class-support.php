<?php
/**
 * Output support tab.
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
class Support {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'sceo_admin_tabs', array( $this, 'add_main_tab' ), 1, 9 );
		add_filter( 'sceo_admin_sub_tabs', array( $this, 'add_main_main_sub_tab' ), 1, 3 );
		add_filter( 'sceo_output_support', array( $this, 'output_main_content' ), 1, 3 );
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
			'get'    => 'support',
			'action' => 'sceo_output_support',
			'url'    => Functions::get_settings_url( 'support' ),
			'label'  => _x( 'Support', 'Tab label as Support', 'ultimate-auto-updates' ),
			'icon'   => 'hands-helping',
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
		if ( ( ! empty( $current_tab ) || ! empty( $sub_tab ) ) && 'support' !== $current_tab ) {
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
		if ( 'support' === $tab ) {
			if ( empty( $sub_tab ) || 'support' === $sub_tab ) {
				?>
				<div class="sce-admin-panel-area">
					<h3 class="sce-panel-heading">
						<?php esc_html_e( 'Get Support On the WordPress Plugin Directory', 'simple-comment-editing-options' ); ?>
					</h3>
					<div class="sce-panel-row">
						<p class="description">
							<?php esc_html_e( 'The best way to receive support is via our contact form.', 'simple-comment-editing-options' ); ?>
						</p>
					</div>
					<div class="sce-panel-row">
						<?php
						$options   = Options::get_options();
						$user      = wp_get_current_user();
						$user_id   = $user->ID;
						$firstname = get_user_meta( $user_id, 'first_name', true );
						$lastname  = get_user_meta( $user_id, 'last_name', true );
						$website   = home_url();

						$support_url = add_query_arg(
							array(
								'firstname' => $firstname,
								'lastname'  => $lastname,
								'site'      => $website,
								'email'     => $user->data->user_email,
								'license'   => $options['license'],
								'product'   => 'Simple Comment Editing',
								'subject'   => 'Simple Comment Editing Options support',
								'support'   => 'Yes',
							),
							'https://mediaron.com/contact/'
						);
						?>
						<a class="sce-button sce-button-info" href="<?php echo esc_url_raw( $support_url ); ?>" target="_blank"><svg class="sce-icon"><use xlink:href="#hands-helping"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Get Support', 'simple-comment-editing-options' ); ?></a>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<h3 class="sce-panel-heading">
						<?php esc_html_e( 'File a GitHub Issue', 'simple-comment-editing-options' ); ?>
					</h3>
					<div class="sce-panel-row">
						<p class="description">
							<?php esc_html_e( 'Feature requests or modifications to existing behavior can be opened on GitHub.', 'simple-comment-editing-options' ); ?>
						</p>
					</div>
					<div class="sce-panel-row">
						<a class="sce-button sce-button-info" href="https://github.com/MediaRon/simple-comment-editing-options/issues" target="_blank"><svg class="sce-icon"><use xlink:href="#sce-github-icon"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Open a GitHub Issue', 'simple-comment-editing-options' ); ?></a>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<h3 class="sce-panel-heading">
						<?php esc_html_e( 'Show Your Support', 'simple-comment-editing-options' ); ?>
					</h3>
					<div class="sce-panel-row">
						<p class="description">
							<?php esc_html_e( 'Please consider upgrading your license if you are on the free plan. A paid license will ensure that SCE and SCEOptions can survive in the long-haul.', 'simple-comment-editing-options' ); ?>
						</p>
					</div>
					<div class="sce-panel-row">
						<a class="sce-button sce-button-info" href="https://mediaron.com/simple-comment-editing-options" target="_blank"><svg class="sce-icon"><use xlink:href="#sce-heart-icon"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Support This Plugin', 'simple-comment-editing-options' ); ?></a>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<h3 class="sce-panel-heading">
						<?php esc_html_e( 'Documentation', 'simple-comment-editing-options' ); ?>
					</h3>
					<div class="sce-panel-row">
						<p class="description">
							<?php esc_html_e( 'The documentation for the plugin displays its capabilities.', 'simple-comment-editing-options' ); ?>
						</p>
					</div>
					<div class="sce-panel-row">
						<a class="sce-button sce-button-info" href="https://mediaron.com/simple-comment-editing/" target="_blank"><svg class="sce-icon"><use xlink:href="#sce-book-icon"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Simple Comment Editing', 'simple-comment-editing-options' ); ?></a>
					</div>
					<div class="sce-panel-row">
						<a class="sce-button sce-button-info" href="https://mediaron.com/simple-comment-editing-options/" target="_blank"><svg class="sce-icon"><use xlink:href="#sce-book-icon"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Simple Comment Editing Options', 'simple-comment-editing-options' ); ?></a>
					</div>
				</div>
				<div class="sce-admin-panel-area">
					<h3 class="sce-panel-heading">
						<?php esc_html_e( 'Help Rate This Plugin', 'simple-comment-editing-options' ); ?>
					</h3>
					<div class="sce-panel-row">
						<p class="description">
							<?php esc_html_e( 'If you find this plugin useful, please consider leaving a star rating on WordPress.org.', 'simple-comment-editing-options' ); ?>
						</p>
					</div>
					<div class="sce-panel-row">
						<a class="sce-button sce-button-info" href="https://wordpress.org/support/plugin/simple-comment-editing/reviews/#new-post" target="_blank"><svg class="sce-icon"><use xlink:href="#sce-star-icon"></use></svg>&nbsp;&nbsp;<?php esc_html_e( 'Help Rate This Plugin', 'simple-comment-editing-options' ); ?></a>
					</div>
				</div>
				<?php
			}
		}
	}
}
