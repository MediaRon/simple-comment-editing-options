<?php
/**
 * Uninstall script
 *
 * Uninstall script for Easy Updates Manager.
 *
 * @package WordPress
 * @since 5.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
delete_site_option( 'sce_options' );
delete_site_option( 'sce_license_status' );
delete_site_option( 'sce_table_version' );

// For table removal.
global $wpdb;
$tablename = $wpdb->base_prefix . 'sce_comments';
$sql       = "drop table if exists $tablename";
$wpdb->query( $sql ); // phpcs:ignore
