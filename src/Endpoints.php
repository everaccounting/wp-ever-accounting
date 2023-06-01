<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Endpoints.
 *
 * @since 1.1.6
 * @package EverAccounting
 * @class Endpoints
 */
class Endpoints extends Singleton {

	/**
	 * Endpoints.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected $endpoints = array();

	/**
	 * Query vars to add to wp.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected $query_vars = array();

	/**
	 * Endpoints constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		// add_action( 'init', array( $this, 'register_endpoints' ), 0 );
		// add_action( 'init', array( $this, 'add_rewrite_rules' ), 0 );
		// add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		// add_filter( 'template_include', array( $this, 'handle_request' ), 0 );
	}

	/**
	 * Register endpoints.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_endpoints() {
		// Endpoints could be a static page, archive or single entry.
		$endpoints = array(
			'payment' => array(
				'title'   => __( 'Payment', 'wp-ever-accounting' ),
				'rewrite' => array(
					'entry_id' => '(?!page/?$)([^/]+)',
				),
			),
		);

		/**
		 * Filter endpoints.
		 *
		 * @param array $endpoints Endpoints.
		 *
		 * @since 1.1.6
		 */
		$endpoints = apply_filters( 'ever_accounting_endpoints', $endpoints );

		// Sanitize endpoints.
		foreach ( $endpoints as $endpoint => $args ) {
			$args['title']            = isset( $args['title'] ) ? $args['title'] : $endpoint;
			$args['query_var']        = ! empty( $args['query_var'] ) ? $args['query_var'] : $endpoint;
			$args['rewrite']          = ! empty( $args['rewrite'] ) ? $args['rewrite'] : array();
			$args['rewrite']['regex'] = ! empty( $args['rewrite']['regex'] ) ? $args['rewrite']['regex'] : false;
			$args['rewrite']['query'] = ! empty( $args['rewrite']['query'] ) ? $args['rewrite']['query'] : false;

			$endpoints[ $endpoint ] = $args;
		}

		$this->endpoints = $endpoints;
	}

	/**
	 * Add rewrite rules.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function add_rewrite_rules() {
		$root = $this->get_endpoint_root();
		foreach ( $this->endpoints as $endpoint => $args ) {
			if ( $args['has_archive'] && $args['has_single'] ) {
				$archive_slug = true === $args['has_archive'] ? $endpoint : $args['has_archive'];
				add_rewrite_rule( "^{$root}/{$endpoint}/(?!page/?$)([^/]+)/?$", "index.php?endpoint={$args['query_var']}&entry_id=\$matches[1]", 'top' );
				add_rewrite_rule( "^{$root}/{$archive_slug}/page/([0-9]+)/?$", "index.php?endpoint={$args['query_var']}&page_type=archive&paged=\$matches[1]", 'top' );
				add_rewrite_rule( "^{$root}/{$archive_slug}/?$", "index.php?endpoint={$endpoint}&page_type=archive&paged=1", 'top' );
			} elseif ( $args['has_archive'] ) {
				$archive_slug = true === $args['has_archive'] ? $endpoint : $args['has_archive'];
				add_rewrite_rule( "^{$root}/{$archive_slug}/page/([0-9]+)/?$", "index.php?endpoint={$args['query_var']}&page_type=archive&paged=\$matches[1]", 'top' );
				add_rewrite_rule( "^{$root}/{$archive_slug}/?$", "index.php?endpoint={$endpoint}&page_type=archive&paged=1", 'top' );
			} elseif ( $args['has_single'] ) {
				add_rewrite_rule( "^{$root}/{$endpoint}/(?!page/?$)([^/]+)/?$", "index.php?endpoint={$args['query_var']}&page_type=single&entry_id=\$matches[1]", 'top' );
			} else {
				add_rewrite_rule( "^{$root}/{$endpoint}/?$", "index.php?endpoint={$endpoint}&page_type=page", 'top' );
			}
		}
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
		$vars[] = 'endpoint';
		$vars[] = 'page_type';
		$vars[] = 'entry_id';

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
		global $wp;
		if ( ! isset( $wp->query_vars['endpoint'] ) ) {
			return $template;
		}
		foreach ( $this->endpoints as $endpoint => $data ) {
			if ( $wp->query_vars['endpoint'] === $data['query_var'] ) {
				$page_type = sanitize_key( $wp->query_vars['page_type'] );
				if ( file_exists( ever_accounting()->get_template_path() . $page_type . '-' . $endpoint . '.php' ) ) {
					$template = ever_accounting()->get_template_path() . $page_type . '-' . $endpoint . '.php';
				} elseif ( file_exists( ever_accounting()->get_template_path() . $page_type . '.php' ) ) {
					$template = ever_accounting()->get_template_path() . $page_type . '.php';
				} else {
					$template = ever_accounting()->get_template_path() . 'home.php';
				}
				/**
				 * Filter template.
				 *
				 * @param string $template Template.
				 * @param string $endpoint Endpoint.
				 *
				 * @since 1.1.6
				 */
				$template = apply_filters( 'ever_accounting_endpoint_template', $template, $endpoint );
				break;
			}
		}

		return $template;
	}

	/**
	 * Get endpoint root.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_endpoint_root() {
		return apply_filters( 'ever_accounting_endpoints_root', get_option( 'endpoints_root', 'accounting' ) );
	}

	/**
	 * Get endpoints.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public function get_endpoints() {
		return $this->endpoints;
	}

	/**
	 * Get endpoint.
	 *
	 * @param string $endpoint Endpoint.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public function get_endpoint( $endpoint = false ) {
		if ( ! $endpoint ) {
			$endpoint = get_query_var( 'endpoint' );
		}

		return isset( $this->endpoints[ $endpoint ] ) ? $this->endpoints[ $endpoint ] : false;
	}

	/**
	 * Get endpoint url.
	 *
	 * @param string $endpoint Endpoint.
	 * @param string $entry_id Entry ID.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_endpoint_url( $endpoint, $entry_id = false ) {
		$root = $this->get_endpoint_root();
		$data = $this->endpoints[ $endpoint ];
		if ( $data['has_single'] && $data['has_archive'] ) {
			if ( ! empty( $entry_id ) ) {
				$url = home_url( "{$root}/{$endpoint}/{$entry_id}/" );
			} else {
				$slug = true === $data['has_archive'] ? $endpoint : $data['has_archive'];
				$url  = home_url( "{$root}/{$slug}/" );
			}
		} elseif ( $data['has_single'] ) {
			$url = home_url( "{$root}/{$endpoint}/{$entry_id}/" );
		} elseif ( $data['has_archive'] ) {
			$slug = true === $data['has_archive'] ? $endpoint : $data['has_archive'];
			$url  = home_url( "{$root}/{$slug}/" );
		} else {
			$url = home_url( "{$root}/{$endpoint}/" );
		}

		return $url;
	}

	/**
	 * Get endpoint title.
	 *
	 * @param string $endpoint Endpoint.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_endpoint_title( $endpoint = '' ) {
		if ( empty( $endpoint ) ) {
			$endpoint = get_query_var( 'endpoint' );
		}
		foreach ( $this->endpoints as $key => $data ) {
			if ( $key === $endpoint ) {
				return $data['title'];
			}
		}

		return '';
	}
}
