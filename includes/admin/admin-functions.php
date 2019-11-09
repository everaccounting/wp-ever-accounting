<?php

defined( 'ABSPATH' ) || exit();

/**
 * Add notice
 * since 1.0.0
 * @param $notice
 * @param string $type
 * @param bool $dismissible
 */
function eaccounting_admin_notice( $notice, $type = 'success', $dismissible = true ) {
	$notices          = get_option( 'eaccounting_admin_notices', array() );
	$dismissible_text = ( $dismissible ) ? "is-dismissible" : "";
	array_push( $notices, array(
		"notice"      => wp_kses( $notice, array(
			'strong' => array(),
			'span'   => array( 'class' => true ),
			'i'      => array( 'class' => true ),
			'a'      => array( 'class' => true, 'href' => true ),
		) ),
		"type"        => $type,
		"dismissible" => $dismissible_text
	) );

	update_option( "eaccounting_admin_notices", $notices );
}

/**
 * Show admin notice
 * since 1.0.0
 */
function eaccounting_flash_admin_notices() {
	$notices = get_option( 'eaccounting_admin_notices', array() );

	foreach ( $notices as $notice ) {
		echo sprintf( '<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
			$notice['type'],
			$notice['dismissible'],
			$notice['notice']
		);
	}

	if ( ! empty( $notices ) && isset($_GET['notice'])) {
		update_option(  'eaccounting_admin_notices', array() );
	}

}
add_action( 'admin_notices', 'eaccounting_flash_admin_notices', 12 );

