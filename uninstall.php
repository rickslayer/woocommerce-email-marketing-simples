<?php
/**
 * WooCommerce Email Marketing Simples
 *
 * Uninstalling Email Marketing Simples deletes tables and options.
 *
 * @version 1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb, $wp_version;

//delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = 'wems_data';");

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wems_emails" );

wp_cache_flush();