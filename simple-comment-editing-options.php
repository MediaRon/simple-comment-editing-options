<?php
/*
Plugin Name: Simple Comment Editing Options
Plugin URI: https://mediaron.com/simple-comment-editing-options
Description: Options for Simple Comment Editing.
Author: Ronald Huereca
Version: 1.0.0
Requires at least: 5.0
Author URI: https://mediaron.com
Contributors: ronalfy
Text Domain: simple-comment-editing-options
Domain Path: /languages
*/
if (!defined('ABSPATH')) die('No direct access.');
define( 'SCE_OPTIONS_VERSION', '1.0.0' );
define( 'SCE_OPTIONS_TABLE_VERSION', '1.0.0' );
define( 'SCE_OPTIONS_SLUG', plugin_basename(__FILE__) );


class SCE_Options {
	private static $instance = null;

	// Minimum PHP version required to run this plugin
	const PHP_REQUIRED = '5.6';

	// Minimum WP version required to run this plugin
	const WP_REQUIRED = '4.9.8';

	//Singleton
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	} //end get_instance

	/**
	 * Class constructor.
	 *
	 * Set up internationalization, auto-loader, and plugin initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {

		$has_errors = false;

		// Check if PHP version is sufficient
		if ( version_compare( PHP_VERSION, self::PHP_REQUIRED, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_insufficient_php' ) );
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'admin_notice_insufficient_php' ) );
			}
			$has_errors = true;
		}

		// Checks if WordPress version is sufficient
		include ABSPATH.WPINC.'/version.php';
		if ( version_compare( $wp_version, self::WP_REQUIRED, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_insufficient_wp' ) );
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'admin_notice_insufficient_wp' ) );
			}
			$has_errors = true;
		}

		if ( ! $has_errors ) {
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		}
	}

	/**
	 * Checks for PHP version.
	 *
	 * Checks if PHP version is sufficient.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_insufficient_php() {
		$this->show_admin_warning(
			__( 'Higher PHP version required', 'simple-comment-editing-options' ),
			sprintf( __( 'The %s plugin requires %s version %s or higher - your current version is only %s.', 'simple-comment-editing-options'), 'Simple Comment Editing Options', 'PHP', self::PHP_REQUIRED, PHP_VERSION ),
			'notice-error'
		);
	}

	/**
	 * Checks for WP version.
	 *
	 * Checks if WP version is sufficient.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_insufficient_wp() {
		include ABSPATH.WPINC.'/version.php';
		$this->show_admin_warning(
			__( 'Higher WordPress version required', 'simple-comment-editing-options' ),
			sprintf( __( 'The %s plugin requires %s version %s or higher - your current version is only %s.', 'simple-comment-editing-options'), 'Simple Comment Editing Options', 'WordPress', self::WP_REQUIRED, $wp_version ),
			'notice-error'
		);
	}

	/**
	 * Checks for SCE is installed.
	 *
	 * Checks for SCE is installed.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_notice_sce_not_installed() {
		$this->show_admin_warning(
			__( 'Simple Comment Editing is required.', 'simple-comment-editing-options' ),
			__( 'The Simple Comment Editing plugin is required for Simple Comment Editing Options to function.', 'simple-comment-editing-options' ),
			'notice-error'
		);
	}

	/**
	 * Shows a dismissible warning notice admin dashboard
	 *
	 * @param string $title   Title of the warning message
	 * @param string $message Warning message in detail
	 * @param string $class   Style class name for warning
	 */
	private function show_admin_warning( $title = "", $message, $class = 'notice-error' ) {
		?>
		<div class="notice is-dismissible <?php echo $class; ?>">
			<p>
				<?php if (!empty($title)) :?>
					<strong>
						<?php echo $title; ?>
					</strong>
				<?php endif;?>
				<span>
					<?php echo $message; ?>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Checks for Simple Comment Editing.
	 *
	 * Checks if SCE is installed
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function is_sce_enabled() {
		if( class_exists( 'Simple_Comment_Editing' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Plugins have been loaded.
	 *
	 * Set up i18n, plugin options.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function plugins_loaded() {
		
		// Check to see if SCE is installed
		if( ! $this->is_sce_enabled() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_sce_not_installed' ) );
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'admin_notice_sce_not_installed' ) );
			}
			return;
		}
		include $this->get_plugin_dir( '/includes/class-sce-admin.php' );
		new SCE_Admin();
	}

	/**
	 * Return absolute path to asset.
	 *
	 * Return absolute path to asset.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $path Relative path to asset
	 * 
	 * @return string Absolute path to asset
	 */
	public function get_plugin_dir( $path = '' ) {
		$dir = rtrim( plugin_dir_path(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;		
	}
	
	/**
	 * Return url path to asset.
	 *
	 * Return url path to asset.
	 *
	 * @since 1.0.0
	 * @access public
	 * 
	 * @param string $path Relative path to asset
	 * 
	 * @return string URL path to asset
	 */
	public function get_plugin_url( $path = '' ) {
		$dir = rtrim( plugin_dir_url(__FILE__), '/' );
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;	
	}
}
/**
 * Initializes plugin and returns main class instance
 *
 * @return SCE_Options
 */
function Simple_Comment_Editing_Options() {
	return SCE_Options::get_instance();
}
Simple_Comment_Editing_Options();