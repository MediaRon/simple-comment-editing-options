<?php
if (!defined('ABSPATH')) die('No direct access.');
class SCE_Admin {

	/**
	 * Holds the slug to the admin panel page
	 *
	 * @since 5.0.0
	 * @static
	 * @var string $slug
	 */
	private static $slug = 'simple-comment-editing-options';

	/**
	 * Holds the URL to the admin panel page
	 *
	 * @since 1.0.0
	 * @static
	 * @var string $url
	 */
	private static $url = '';

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}
	
	/**
	 * Initializes admin menus, plugin settings links, tables, etc.
	 *
	 * @since 1.0.0
	 * @access public
	 * @see __construct
	 */
	public function init() {
		$prefix = is_multisite() ? 'network_admin_' : '';
		add_action( $prefix . 'plugin_action_links_' . SCE_OPTIONS_SLUG, array( $this, 'plugin_settings_link' ) );
		add_action( $prefix . 'plugin_action_links_' . SCE_SLUG, array( $this, 'plugin_settings_link' ) );
	}

	/**
	 * Adds plugin settings page link to plugin links in WordPress Dashboard Plugins Page
	 *
	 * @since 1.0.0
	 * @access public
	 * @see __construct
	 * @param array $settings Uses $prefix . "plugin_action_links_$plugin_file" action
	 * @return array Array of settings
	 */
	public function plugin_settings_link( $settings ) {
		$admin_anchor = sprintf('<a href="%s">%s</a>', esc_url($this->get_url()), esc_html__('Settings', 'stops-core-theme-and-plugin-updates'));
		if (! is_array( $settings  )) {
			return array( $admin_anchor );
		} else {
			return array_merge( array( $admin_anchor ), $settings) ;
		}
	}

	/**
	 * Return the URL to the admin panel page.
	 *
	 * Return the URL to the admin panel page.
	 *
	 * @since 5.0.0
	 * @access static
	 *
	 * @return string URL to the admin panel page.
	 */
	public static function get_url() {
		$url = self::$url;
		if (empty($url)) {
			if (is_multisite()) {
				$url = add_query_arg(array( 'page' => self::$slug ), network_admin_url('settings.php'));
			} else {
				$url = add_query_arg(array( 'page' => self::$slug ), admin_url('options-general.php'));
			}
			self::$url = $url;
		}
		return $url;
	}
}