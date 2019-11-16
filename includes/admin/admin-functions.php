<?php
defined( 'ABSPATH' ) || exit();

/**
 * Get admin view
 * since 1.0.0
 * @param $template_name
 * @param array $args
 */
function eaccounting_get_views( $template_name, $args = [] ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	if ( file_exists( EACCOUNTING_ADMIN_ABSPATH . '/views/' . $template_name ) ) {
		include EACCOUNTING_ADMIN_ABSPATH . '/views/' . $template_name;
	}
}



/**
 * Add admin notice
 * since 1.0.0
 * @param $notice
 * @param string $type
 * @param bool $dismissible
 */
function eaccounting_admin_notice( $notice, $type = 'success', $dismissible = true ) {
	$notices = EAccounting_Admin_Notices::instance();
	$notices->add($notice, $type, $dismissible);
}
