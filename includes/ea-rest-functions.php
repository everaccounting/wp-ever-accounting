<?php
/**
 * EverAccounting REST Functions
 *
 * Functions for REST specific things.
 *
 * @package EverAccounting\Functions
 * @version 1.1.0
 */
defined( 'ABSPATH' ) || die();

/**
 * Parses and formats a date for ISO8601/RFC3339.
 *
 * Required WP 4.4 or later.
 * See https://developer.wordpress.org/reference/functions/mysql_to_rfc3339/
 *
 * @since  1.1.0
 * @param  string|null|\EverAccounting\DateTime $date Date.
 * @param  bool                    $utc  Send false to get local/offset time.
 * @return string|null ISO8601/RFC3339 formatted datetime.
 */
function eaccounting_rest_date_response( $date, $utc = true ) {
	if ( is_numeric( $date ) ) {
		$date = new \EverAccounting\DateTime( "@$date", new \DateTimeZone( 'UTC' ) );
		$date->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	} elseif ( is_string( $date ) ) {
		$date = new \EverAccounting\DateTime( $date, new \DateTimeZone( 'UTC' ) );
		$date->setTimezone( new DateTimeZone( wc_timezone_string() ) );
	}

	if ( ! is_a( $date, '\EverAccounting\DateTime' ) ) {
		return null;
	}

	// Get timestamp before changing timezone to UTC.
	return gmdate( 'Y-m-d\TH:i:s', $utc ? $date->getTimestamp() : $date->getOffsetTimestamp() );
}


/**
 * Makes internal API request for usages within PHP
 *
 *
 *
 * @since 1.0.2
 *
 * @param        $endpoint
 *
 * @param array  $args
 * @param string $method
 *
 * @param string $namespace
 *
 * @return array
 */
function eaccounting_rest_request( $endpoint, $args = array(), $method = 'GET', $namespace = '/ea/v1/' ) {
	$endpoint = $namespace . untrailingslashit( ltrim( $endpoint, '/' ) );
	if ( ! empty( $args['id'] ) ) {
		$endpoint .= '/' . intval( $args['id'] );
		unset( $args['id'] );
	}

	$request = new \WP_REST_Request( $method, $endpoint );
	$request->set_query_params( $args );
	$response = rest_do_request( $request );
	$server   = rest_get_server();
	$result   = $server->response_to_data( $response, false );

	return $result;
}
