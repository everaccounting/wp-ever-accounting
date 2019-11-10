<?php

defined( 'ABSPATH' ) || exit();

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

