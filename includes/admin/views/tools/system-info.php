<?php
/**
 * Displays the system info report.
 *
 * @since 1.0.2
 *
 * @return string The compiled system info report.
 */
function eaccounting_tools_system_info_report() {

	global $wpdb;

	// Get theme info
	$theme_data = wp_get_theme();
	$theme      = $theme_data->Name . ' ' . $theme_data->Version;

	$return = '### Begin System Info ###' . "\n\n";

	// Start with the basics...
	$return .= '-- Site Info' . "\n\n";
	$return .= 'Site URL:                 ' . site_url() . "\n";
	$return .= 'Home URL:                 ' . home_url() . "\n";
	$return .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . "\n";

	$locale = get_locale();

	// WordPress configuration
	$return .= "\n" . '-- WordPress Configuration' . "\n\n";
	$return .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
	$return .= 'Language:                 ' . ( empty( $locale ) ? 'en_US' : $locale ) . "\n";
	$return .= 'Permalink Structure:      ' . ( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : 'Default' ) . "\n";
	$return .= 'Active Theme:             ' . $theme . "\n";
	$return .= 'Show On Front:            ' . get_option( 'show_on_front' ) . "\n";

	// Only show page specs if frontpage is set to 'page'
	if ( get_option( 'show_on_front' ) === 'page' ) {
		$front_page_id = get_option( 'page_on_front' );
		$blog_page_id  = get_option( 'page_for_posts' );

		$return .= 'Page On Front:            ' . ( $front_page_id != 0 ? get_the_title( $front_page_id ) . ' (#' . $front_page_id . ')' : 'Unset' ) . "\n";
		$return .= 'Page For Posts:           ' . ( $blog_page_id != 0 ? get_the_title( $blog_page_id ) . ' (#' . $blog_page_id . ')' : 'Unset' ) . "\n";
	}

	$return .= 'ABSPATH:                  ' . ABSPATH . "\n";
	$return .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . '   Status: ' . ( strlen( $wpdb->prefix ) > 16 ? 'ERROR: Too long' : 'Acceptable' ) . "\n";
	$return .= 'WP_DEBUG:                 ' . ( defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set' ) . "\n";
	$return .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
	$return .= 'Registered Post Statuses: ' . implode( ', ', get_post_stati() ) . "\n";

	//
	// EverAccounting
	//

	$settings      = eaccounting()->settings;
	$account_id    = eaccounting()->settings->get( 'default_account' );
	$currency_code = eaccounting()->settings->get( 'default_currency' );
	$currency      = '';
	$db_version    = get_option( 'eaccounting_version' );
	$install_date  = get_option( 'eaccounting_install_date' );
	global $wpdb;
	$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}ea_%'" );
	$tables = preg_replace( "/^{$wpdb->prefix}/", '', $tables );

	// Configariotn settings.
	$return .= "\n" . '-- EverAccounting Configuration' . "\n\n";
	$return .= 'Version:                          ' . eaccounting()->get_version() . "\n";
	$return .= 'DB Version:                       ' . ( $db_version ? "$db_version\n" : "Unset\n" );
	$return .= 'Install Date:                     ' . ( $install_date ? date( 'Y-m-d H:i:s' ) . "\n" : "Unset\n" );
	$return .= 'Debug Mode:                       ' . ( $settings->get( 'debug_mode', false ) ? 'True' . "\n" : "False\n" );
	$return .= 'Accounts Table:                   ' . ( in_array( 'ea_accounts', $tables, true ) ? 'True' . "\n" : "False\n" );
	$return .= 'Transactions Table:               ' . ( in_array( 'ea_transactions', $tables, true ) ? 'True' . "\n" : "False\n" );
	$return .= 'Contacts Table:                   ' . ( in_array( 'ea_contacts', $tables, true ) ? 'True' . "\n" : "False\n" );
	$return .= 'Currencies Table:                 ' . ( in_array( 'ea_currencies', $tables, true ) ? 'True' . "\n" : "False\n" );
	$return .= 'Transfers Table:                  ' . ( in_array( 'ea_transfers', $tables, true ) ? 'True' . "\n" : "False\n" );
	$return .= 'Categories Table:                 ' . ( in_array( 'ea_categories', $tables, true ) ? 'True' . "\n" : "False\n" );

	// Misc Settings
	$return .= "\n" . '-- EverAccounting Misc Settings' . "\n\n";

	// Object counts.
	$return .= "\n" . '-- EverAccounting Object Counts' . "\n\n";
	$return .= 'Transactions:                     ' . number_format( \EverAccounting\Transactions\query()->count(), false ) . "\n";
	$return .= 'Accounts:                         ' . number_format( \EverAccounting\Accounts\query()->count(), false ) . "\n";
	$return .= 'Contacts:                         ' . number_format( \EverAccounting\Contacts\query()->count(), false ) . "\n";
	$return .= 'Currencies:                       ' . number_format( \EverAccounting\Currencies\query()->count(), false ) . "\n";
	$return .= 'Categories:                       ' . number_format( \EverAccounting\Categories\query()->count(), false ) . "\n";

	// Get plugins that have an update
	$updates = get_plugin_updates();

	// Must-use plugins
	// NOTE: MU plugins can't show updates!
	$muplugins = get_mu_plugins();
	if ( count( $muplugins ) > 0 ) {
		$return .= "\n" . '-- Must-Use Plugins' . "\n\n";

		foreach ( $muplugins as $plugin => $plugin_data ) {
			$return .= $plugin_data['Name'] . ': ' . $plugin_data['Version'] . "\n";
		}
	}

	// WordPress active plugins
	$return .= "\n" . '-- WordPress Active Plugins' . "\n\n";

	$plugins        = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	foreach ( $plugins as $plugin_path => $plugin ) {
		if ( ! in_array( $plugin_path, $active_plugins ) ) {
			continue;
		}

		$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
		$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
	}

	// WordPress inactive plugins
	$return .= "\n" . '-- WordPress Inactive Plugins' . "\n\n";

	foreach ( $plugins as $plugin_path => $plugin ) {
		if ( in_array( $plugin_path, $active_plugins ) ) {
			continue;
		}

		$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
		$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
	}

	if ( is_multisite() ) {
		// WordPress Multisite active plugins
		$return .= "\n" . '-- Network Active Plugins' . "\n\n";

		$plugins        = wp_get_active_network_plugins();
		$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

		foreach ( $plugins as $plugin_path ) {
			$plugin_base = plugin_basename( $plugin_path );

			if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
				continue;
			}

			$update  = ( array_key_exists( $plugin_path, $updates ) ) ? ' (needs update - ' . $updates[ $plugin_path ]->update->new_version . ')' : '';
			$plugin  = get_plugin_data( $plugin_path );
			$return .= $plugin['Name'] . ': ' . $plugin['Version'] . $update . "\n";
		}
	}

	// Server configuration (really just versioning)
	$return .= "\n" . '-- Webserver Configuration' . "\n\n";
	$return .= 'PHP Version:              ' . PHP_VERSION . "\n";
	$return .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
	$return .= 'Webserver Info:           ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
	$return .= 'SSL Configured:           ' . ( is_ssl() ? 'Yes' : 'No' ) . "\n";

	// PHP configuration
	$return .= "\n" . '-- PHP Configuration' . "\n\n";
	$return .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
	$return .= 'Upload Max Size:          ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
	$return .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
	$return .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
	$return .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
	$return .= 'Display Errors:           ' . ( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' ) . "\n";

	// PHP extensions and such
	$return .= "\n" . '-- PHP Extensions' . "\n\n";
	$return .= 'cURL:                     ' . ( function_exists( 'curl_init' ) ? 'Supported' : 'Not Supported' ) . "\n";
	$return .= 'fsockopen:                ' . ( function_exists( 'fsockopen' ) ? 'Supported' : 'Not Supported' ) . "\n";
	$return .= 'SOAP Client:              ' . ( class_exists( 'SoapClient' ) ? 'Installed' : 'Not Installed' ) . "\n";
	$return .= 'Suhosin:                  ' . ( extension_loaded( 'suhosin' ) ? 'Installed' : 'Not Installed' ) . "\n";

	$return .= "\n" . '### End System Info ###';

	return $return;
}