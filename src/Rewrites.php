<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Rewrites.
 *
 * @since 1.1.6
 * @package EverAccounting
 * @class Rewrites
 */
class Rewrites extends Singleton {

	/**
	 * Endpoints constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'register_endpoints' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 99 );
		add_filter( 'template_include', array( $this, 'handle_request' ), 99 );
		add_filter( 'locate_template', array( $this, 'locate_template' ), 99, 3 );
	}

	/**
	 * Register endpoints.
	 *
	 * @since 1.1.6
	 */
	public function register_endpoints() {
		$base = $this->get_endpoint_base();
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
	public function add_query_vars( $vars ) {
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
	public function handle_request( $template ) {
		$page = get_query_var( 'eac_page' );
		$uuid = get_query_var( 'uuid' );

		// If the page and uuid are not set, bail.
		if ( empty( $page ) || empty( $uuid ) ) {
			return $template;
		}

		switch ( $page ) {
			case 'invoice':
			case 'payment':
			case 'bill':
			case 'expense':
				$template = EAC()->get_template_path() . $page . '.php';
				break;
		}

		return $template;
	}

	/**
	 * Locate template.
	 *
	 * @param string $template Template.
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function locate_template( $template, $template_name, $template_path ) {
		// Add our header-eac.php to the top of the stack.
		if ( 'header-eac.php' === $template_name ) {
			$template = EAC()->get_template_path() . 'global/header-eac.php';
		}

		return $template;
	}

	/**
	 * Get endpoint base.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_endpoint_base() {
		return apply_filters( 'ever_accounting_endpoints_base', 'eac' );
	}
}
