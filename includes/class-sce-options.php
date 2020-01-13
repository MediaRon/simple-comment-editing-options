<?php
if (!defined('ABSPATH')) die('No direct access.');
class SCE_Plugin_Options {

	/**
	 * Holds the options variable.
	 *
	 * @since 5.0.0
	 * @static
	 * @var string $slug
	 */
	private static $options = array();

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	} //end get_instance

	public function __construct() {
	}

	/**
	 * Update options via sanitization
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $options array of options to save
	 * @return void
	 */
	public function update_options( $options ) {
		foreach ( $options as $key => &$option ) {
			switch ( $key ) {
				case 'timer':
					$timer = absint( $options[ $key ] );
					if ( 0 === $timer ) {
						$timer = 5;
					}
					$option = $timer;
					break;
				case 'min_comment_length':
					$option = absint( $options[ $key ] );
					break;
				case 'max_comment_length':
					$option = absint( $options[ $key ] );
					break;
				case 'require_comment_length':
				case 'require_comment_length_max':
				case 'allow_delete_confirmation':
				case 'allow_delete':
				case 'show_timer':
				case 'allow_edit_notification':
				case 'show_icons':
				case 'show_stop_timer':
				case 'allow_unlimited_editing':
				case 'allow_front_end_character_limit':
					$option = filter_var( $options[ $key ], FILTER_VALIDATE_BOOLEAN );
					break;
				case 'allow_comment_logging':
					$option = filter_var( $options[ $key ], FILTER_VALIDATE_BOOLEAN );
					if ( true === $option ) {
						require_once SCE_Options::get_instance()->get_plugin_dir( '/includes/class-sce-table-create.php' );
						$table_create = new SCE_Table_Create();
						$table_create->create_table();
					} else {
						require_once SCE_Options::get_instance()->get_plugin_dir( '/includes/class-sce-table-create.php' );
						$table_create = new SCE_Table_Create();
						$table_create->drop();
					}
					break;
				default:
					$option = sanitize_text_field( $options[ $key ] );
					break;
			}
		}
		update_site_option( 'sce_options', $options );
	}

	public function get_options() {
		$options  = get_site_option( 'sce_options', array() );
		$defaults = $this->get_defaults();
		if ( count( $options ) < count( $defaults ) ) {
			$options = wp_parse_args( $options, $defaults );
		}
		return $options;
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
			'timer'                           => 5,
			'show_timer'                      => true,
			'loading_image'                   => Simple_Comment_Editing::get_instance()->get_plugin_url( '/images/loading.gif' ),
			'allow_delete'                    => true,
			'button_theme'                    => 'default',
			'show_icons'                      => false,
			'click_to_edit_text'              => __( 'Click to Edit', 'simple-comment-editing' ),
			'show_timer'                      => true,
			'save_text'                       => __( 'Save', 'simple-comment-editing' ),
			'cancel_text'                     => __( 'Cancel', 'simple-comment-editing' ),
			'delete_text'                     => __( 'Delete', 'simple-comment-editing' ),
			'custom_class'                    => '',
			'allow_delete_confirmation'       => true,
			'allow_edit_notification'         => false,
			'edit_notification_to'            => is_multisite() ? get_site_option( 'admin_email' ) : get_option(
				'admin_email'
			),
			'edit_notification_from'          => is_multisite() ? get_site_option( 'admin_email' ) : get_option(
				'admin_email'
			),
			'edit_notification_subject'       => sprintf( __( 'A user has edited a comment on %s', 'simple-comment-editing-options' ), is_multisite() ? get_site_option( 'site_name' ) : get_option( 'blogname' ) ),
			'edit_text'                       => __( 'Click to Edit', 'simple-comment-editing' ),
			'confirm_delete'                  => __( 'Do you want to delete this comment?', 'simple-comment-editing' ),
			'comment_deleted'                 => __( 'Your comment has been removed.', 'simple-comment-editing' ),
			'comment_deleted_error'           => __( 'Your comment could not be deleted', 'simple-comment-editing' ),
			'comment_empty_error'             => Simple_Comment_Editing::get_instance()->errors->get_error_message( 'comment_empty' ),
			'require_comment_length'          => false,
			'min_comment_length'              => 50,
			'require_comment_length_max'      => false,
			'max_comment_length'              => 2000,
			'allow_comment_logging'           => false,
			'show_stop_timer'                 => false,
			'stop_timer_text'                 => __( 'Cancel Timer', 'simple-comment-editing-options' ),
			'timer_appearance'                => 'words',
			'license'                         => '',
			'allow_unlimited_editing'         => false,
			'allow_front_end_character_limit' => false,
		);
		return $defaults;
	}
	
}