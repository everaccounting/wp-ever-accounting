<?php

namespace EverAccounting\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Rewrites.
 *
 * @since 1.1.6
 * @package EverAccounting
 * @class Rewrites
 */
class Rewrites {

	/**
	 * Endpoints constructor.
	 *
	 * @since 1.1.6
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_endpoints' ), 0 );
		add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ), 99 );
		add_filter( 'template_include', array( __CLASS__, 'handle_request' ), 99 );
	}

	/**
	 * Register endpoints.
	 *
	 * @since 1.1.6
	 */
	public static function register_endpoints() {
		$base = apply_filters( 'eac_endpoints_base', 'eac' );
		// Invoice endpoints.
		add_rewrite_rule( "^{$base}/invoice/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=invoice', 'top' );
		add_rewrite_rule( "^{$base}/bill/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=bill', 'top' );
		// Payment endpoints.
		add_rewrite_rule( "^{$base}/payment/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=payment', 'top' );
		add_rewrite_rule( "^{$base}/expense/([^/]+)/?$", 'index.php?uuid=$matches[1]&eac_page=expense', 'top' );
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public static function add_query_vars( $vars ) {
		$vars[] = 'output';
		$vars[] = 'uuid';
		$vars[] = 'eac_page';

		return $vars;
	}

	/**
	 * Handle request.
	 *
	 * @param string $template Template.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public static function handle_request( $template ) {
		$page = get_query_var( 'eac_page' );
		$uuid = get_query_var( 'uuid' );

		// If the page and uuid are not set, bail.
		if ( empty( $page ) || empty( $uuid ) ) {
			return $template;
		}

		if ( has_action( "eac_output_{$page}" ) ) {

			// No cache headers.
			nocache_headers();

			// Block Search Engine Indexing.
			header( 'X-Robots-Tag: noindex, nofollow', true );

			/**
			 * Fire an action before the template is loaded.
			 *
			 * @since 2.0.0
			 */
			do_action( "eac_output_{$page}", $uuid );

			// Done, clear buffer and exit.
			die();
		}

		return $template;
	}
}
