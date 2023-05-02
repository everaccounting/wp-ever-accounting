<?php
/**
 * EverAccounting Uninstall
 *
 * Uninstalling EverAccounting deletes user roles, tables, and options.
 *
 * @package EverAccounting\Uninstaller
 * @version 1.1.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// clear events.
wp_clear_scheduled_hook( 'eac_twicedaily_scheduled_event' );
wp_clear_scheduled_hook( 'eac_daily_scheduled_event' );
wp_clear_scheduled_hook( 'eac_weekly_scheduled_event' );

/*
 * Only remove ALL product and page data if EACCOUNTING_REMOVE_ALL_DATA constant is set to true in user's
 * wp-config.php. This is to prevent data loss when deleting the plugin from the backend
 * and to ensure only the site owner can perform this action.
 */
if ( defined( 'EAC_REMOVE_ALL_DATA' ) && true === EAC_REMOVE_ALL_DATA ) {
	global $wpdb;

	// Roles.
	\EverAccounting\Install::remove_roles();

	// Tables.
	\EverAccounting\Install::drop_tables();

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'eaccounting\_%';" );

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
