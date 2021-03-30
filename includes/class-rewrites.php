<?php
/**
 * EverAccounting  Rewrites Event Handlers.
 *
 * @since       1.1.0
 * @package     EverAccounting
 * @class       Rewrites
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Rewrites
 * @package EverAccounting
 */
class Rewrites {
	/**
	 * Url base.
	 *
	 * @var string
	 */
	public $url_base;

	/**
	 * EverAccounting_Rewrites constructor.
	 */
	public function __construct() {
		$this->url_base = apply_filters( 'eaccounting_url_base', 'eaccounting' );
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
			add_action( 'template_redirect', array( $this, 'rewrite_templates' ), -1 );
		}
	}


	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		return apply_filters( 'eaccounting_get_query_vars', array(
			$this->url_base,
			'invoices',
			'bills',
			'singular',
			'id',
			'gateway',
			'paged',
			'endpoint'
		) );
	}

	/**
	 * Add the required rewrite rules
	 *
	 * @return void
	 */
	function add_endpoints() {
		add_rewrite_rule( "^{$this->url_base}/?$", 'index.php?eaccounting=true&endpoint=dashboard', 'top' );
		add_rewrite_rule( "^{$this->url_base}/([^/]+)?/?$", 'index.php?eaccounting=true&endpoint=$matches[1]', 'top' );
		add_rewrite_rule( "^{$this->url_base}/([^/]+)/([0-9]+)/(.*)?/?$", 'index.php?eaccounting=true&endpoint=$matches[1]&id=$matches[2]&singular=true', 'top' );
		add_rewrite_rule( "^{$this->url_base}/([^/]+)/([0-9]+)/?$", 'index.php?eaccounting=true&endpoint=$matches[1]&id=$matches[2]&singular=true', 'top' );
		add_rewrite_rule( "^{$this->url_base}/([^/]+)/page/([0-9]+)?/?$", 'index.php?eaccounting=true&endpoint=$matches[1]&paged=$matches[2]', 'top' );
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $var ) {
			$vars[] = $var;
		}

		return $vars;
	}


	/**
	 * Load our template on our rewrite rule
	 *
	 * @return void
	 */
	public function rewrite_templates() {
		if ( 'true' === get_query_var( 'eaccounting' ) ) {
			ob_start();

			$endpoint = get_query_var('endpoint', 'dashboard');

			do_action('eaccounting_redirect', $endpoint);

			do_action('eaccounting_page_header', $endpoint);

			do_action('eaccounting_page_header_'.$endpoint );

			if (  has_action( 'eaccounting_page_content_' . $endpoint ) ) {
				do_action( 'eaccounting_page_content_' . $endpoint );
			}else{
				do_action( 'eaccounting_page_content_unauthorized' );
			}

			do_action('eaccounting_page_footer_'.$endpoint );

			do_action('eaccounting_page_footer', $endpoint);

			exit();
		}
	}

}

new Rewrites();
