<?php

class EverAccounting_Rewrites {

	/**
	 * EverAccounting_Rewrites constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'register_query_var' ) );
		add_action( 'template_redirect', array( $this, 'rewrite_templates' ) );
		add_action( 'eaccounting_body', array( $this, 'render_page' ) );
	}

	/**
	 * Add the required rewrite rules
	 *
	 * @return void
	 */
	function add_rewrite_rules() {
		$eaccounting_slug = 'eaccounting';
		//add_rewrite_rule( '^' . $eaccounting_slug . '/?$', 'index.php?eaccounting=true', 'top' );
		add_rewrite_rule( '^' . $eaccounting_slug . '/invoice/([0-9]{1,})/(.*)?/?$', 'index.php?eaccounting=true&ea_page=invoice&invoice_id=$matches[1]&key=$matches[2]', 'top' );
		add_rewrite_rule( '^' . $eaccounting_slug . '/bill/([0-9]{1,})/(.*)?/?$', 'index.php?eaccounting=true&ea_page=bill&bill_id=$matches[1]&key=$matches[2]', 'top' );
	}

	/**
	 * Register our query vars
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	function register_query_var( $vars ) {
		$vars[] = 'eaccounting';
		$vars[] = 'ea_page';
		$vars[] = 'key';
		$vars[] = 'invoice_id';
		$vars[] = 'bill_id';
		return $vars;
	}

	/**
	 * Load our template on our rewrite rule
	 *
	 * @return void
	 */
	public function rewrite_templates() {
		if ( 'true' === get_query_var( 'eaccounting' ) ) {
			eaccounting_get_template( 'eaccounting.php' );
			exit();
		}
	}

	public function render_page() {
		$ea_page = get_query_var( 'ea_page' );
		$key     = get_query_var( 'key' );
		switch ( $ea_page ) {
			case 'invoice':
				$id       = get_query_var( 'invoice_id' );
				$template = 'single-invoice.php';
				eaccounting_get_template($template, array('invoice_id' => $id, 'key' => $key ));
				break;
			case 'bill':
				$id       = get_query_var( 'bill_id' );
				$template = 'single-bill.php';
				eaccounting_get_template($template, array('bill_id' => $id, 'key' => $key ));
				break;
			default:
				wp_redirect(site_url());
				break;
		}
	}
}

new EverAccounting_Rewrites();