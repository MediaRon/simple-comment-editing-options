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
class Translations {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'sceo_admin_tabs', array( $this, 'add_main_tab' ), 1, 7 );
		add_filter( 'sceo_admin_sub_tabs', array( $this, 'add_main_main_sub_tab' ), 1, 3 );
		add_filter( 'sceo_output_translations', array( $this, 'output_main_content' ), 1, 3 );
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
			'get'    => 'translations',
			'action' => 'sceo_output_translations',
			'url'    => Functions::get_settings_url( 'translations' ),
			'label'  => _x( 'Translations', 'Tab label as Translations', 'ultimate-auto-updates' ),
			'icon'   => 'language',
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
		if ( 'translations' === $tab ) {
			if ( empty( $sub_tab ) || 'translations' === $sub_tab ) {
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
						<h2><?php esc_html_e( 'Translations', 'simple-comment-editing-options' ); ?></h2>
						<p class="description"><?php esc_html_e( 'Edit the text for Simple Comment Editing below.', 'simple-comment-editing-options' ); ?></p>
						<form action="" method="POST">
							<?php wp_nonce_field( 'save_sce_options' ); ?>
							<table class="form-table">
								<tbody>
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
