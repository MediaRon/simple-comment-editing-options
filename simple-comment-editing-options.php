<?php
/*
Plugin Name: Simple Comment Editing Options
Plugin URI: https://mediaron.com/simple-comment-editing-options
Description: Options for Simple Comment Editing.
Author: Ronald Huereca
Version: 1.1.0
Requires at least: 5.0
Author URI: https://mediaron.com
Contributors: ronalfy
Text Domain: simple-comment-editing-options
Domain Path: /languages
*/
if (!defined('ABSPATH')) die('No direct access.');
define( 'SCE_OPTIONS_VERSION', '1.1.0' );
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
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 9 );
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
	 * @since 1.0.3
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
	 * Add scripts to SCE options front-end
	 *
	 * Add scriptss to SCE options front-end
	 *
	 * @since 1.0.4
	 * @access public
	 */
	public function add_scripts() {
		wp_enqueue_script( 'sce-options', plugins_url( '/js/simple-comment-editing-options.js', __FILE__ ), array( 'wp-hooks', 'simple-comment-editing' ), SCE_OPTIONS_VERSION, true );
		$options = get_site_option( 'sce_options', false );
		$show_stop_timer = isset( $options['show_stop_timer'] ) ? $options['show_stop_timer'] : false;
		$stop_timer_text = isset( $options['stop_timer_text'] ) ? $options['stop_timer_text'] : __( 'Cancel Timer', 'simple-comment-editing-options' );
		$timer_appearance = isset( $options['timer_appearance'] ) ? $options['timer_appearance'] : 'words';
		wp_localize_script( 'sce-options', 'sce_options', array(
			'show_stop_timer'  => $show_stop_timer,
			'stop_timer_text'  => $stop_timer_text,
			'stop_timer_svg'   => apply_filters( 'sce_button_extra_stop_timer', '' ),
			'timer_appearance' => $timer_appearance,
		) );
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

		// Prevent double loading admin/classnames
		add_filter( 'sce_show_admin', '__return_false' );

		// Check to see if SCE is installed
		if( ! $this->is_sce_enabled() ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_sce_not_installed' ) );
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'admin_notice_sce_not_installed' ) );
			}
			return;
		}
		if( is_admin() ) {
			include $this->get_plugin_dir( '/includes/class-sce-admin.php' );
			$this->admin = new SCE_Admin();
		}
		include $this->get_plugin_dir( '/includes/class-sce-output.php' );
		$this->output = new SCE_Output();

		include $this->get_plugin_dir( '/includes/class-sce-frontend-editing.php' );
		$this->output = new SCE_Frontend_Editing();

		add_action( 'sce_scripts_loaded', array( $this, 'add_scripts' ) );

		add_action( 'init', array( $this, 'setup_ajax_calls' ) );

		// Auto Update class
		add_action( 'admin_init', array( $this, 'sce_plugin_updater' ), 0 );

		// Load text domain
		load_plugin_textdomain( 'simple-comment-editing-options', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Allow for automatic updates.
	 *
	 * Allow for automatic updates.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function sce_plugin_updater() {
		require_once $this->get_plugin_dir( '/includes/EDD_SL_Plugin_Updater.php' );
		$options = get_site_option( 'sce_options' );
		$license_status = get_site_option( 'sce_license_status', false );
		if ( isset( $options['license'] ) && false !== $license_status ) {
			// setup the updater
			$edd_updater = new EDD_SL_Plugin_Updater( 'https://mediaron.com', __FILE__,
				array(
					'version' => SCE_OPTIONS_VERSION,
					'license' => $options['license'],
					'item_id' => 797,
					'author'  => 'Ronald Huereca',
					'beta'    => false,
					'url'     => home_url(),
				)
			);
		}
	}

	/**
	 * Sets up Ajax calls.
	 *
	 * Sets up Ajax calls.
	 *
	 * @since 1.1.0
	 * @access public
	 */
	public function setup_ajax_calls() {
		add_action( 'wp_ajax_sce_restore_comment', array( $this, 'ajax_restore_comment' ) );
	}

	/**
	 * Restores a comment.
	 *
	 * Restores a comment.
	 *
	 * @since 1.0.7
	 * @access public
	 */
	public function ajax_restore_comment() {
		$nonce = $_POST['nonce'];
		$comment_id = absint( $_POST['comment_id'] );
		$record_id = absint( $_POST['id'] );

		// Do a permissions check
		if ( ! current_user_can( 'moderate_comments' ) ) {
			$return = array(
				'errors' => true
			);
			wp_send_json( $return );
			exit;
		}

		// Verify nonce
		if ( ! wp_verify_nonce( $nonce, 'restore-comment-' . $comment_id ) ) {
			$return = array(
				'errors' => true
			);
			wp_send_json( $return );
			exit;
		}

		// Get record ID
		global $wpdb;
		$tablename = $wpdb->base_prefix . 'sce_comments';
		$query = "select comment_content from $tablename where id = %d";
		$query = $wpdb->prepare( $query, $record_id );
		$results = $wpdb->get_row( $query );
		$comment_text = $results->comment_content;

		// Now update the comment
		$comment_to_save = get_comment( $comment_id, ARRAY_A );
		$comment_to_save['comment_content'] = $comment_text;
		wp_update_comment( $comment_to_save );
		die( $comment_text );
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