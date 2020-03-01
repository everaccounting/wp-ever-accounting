<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Page_Controller {

	// JS-powered page root.
	const PAGE_ROOT = 'eaccounting';

	/**
	 * Singleton instance of self.
	 *
	 * @var EAccounting_Page_Controller
	 */
	private static $instance = false;

	/**
	 * Current page ID (or false if not registered with this controller).
	 *
	 * @var string
	 */
	private $current_page = null;

	/**
	 * Registered pages
	 * Contains information (breadcrumbs, menu info) about JS powered pages and classic WooCommerce pages.
	 *
	 * @var array
	 */
	private $pages = array();

	/**
	 * We want a single instance of this class so we can accurately track registered menus and pages.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Connect an existing page to wc-admin.
	 *
	 * @param array $options {
	 *   Array describing the page.
	 *
	 *   @type string       id           Id to reference the page.
	 *   @type string|array title        Page title. Used in menus and breadcrumbs.
	 *   @type string|null  parent       Parent ID. Null for new top level page.
	 *   @type string       path         Path for this page. E.g. admin.php?page=wc-settings&tab=checkout
	 *   @type string       capability   Capability needed to access the page.
	 *   @type string       icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
	 *   @type int          position     Menu item position.
	 *   @type boolean      js_page      If this is a JS-powered page.
	 * }
	 */
	public function connect_page( $options ) {
		if ( ! is_array( $options['title'] ) ) {
			$options['title'] = array( $options['title'] );
		}

		/**
		 * Filter the options when connecting or registering a page.
		 *
		 * Use the `js_page` option to determine if registering.
		 *
		 * @param array $options {
		 *   Array describing the page.
		 *
		 *   @type string       id           Id to reference the page.
		 *   @type string|array title        Page title. Used in menus and breadcrumbs.
		 *   @type string|null  parent       Parent ID. Null for new top level page.
		 *   @type string       screen_id    The screen ID that represents the connected page. (Not required for registering).
		 *   @type string       path         Path for this page. E.g. admin.php?page=wc-settings&tab=checkout
		 *   @type string       capability   Capability needed to access the page.
		 *   @type string       icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
		 *   @type int          position     Menu item position.
		 *   @type boolean      js_page      If this is a JS-powered page.
		 * }
		 */
		$options = apply_filters( 'eaccounting_navigation_connect_page_options', $options );

		$this->pages[ $options['id'] ] = $options;
	}

	/**
	 * Returns the path from an ID.
	 *
	 * @param  string $id  ID to get path for.
	 * @return string Path for the given ID, or the ID on lookup miss.
	 */
	public function get_path_from_id( $id ) {
		if ( isset( $this->pages[ $id ] ) && isset( $this->pages[ $id ]['path'] ) ) {
			return $this->pages[ $id ]['path'];
		}
		return $id;
	}

	/**
	 * Adds a JS powered page to wc-admin.
	 *
	 * @param array $options {
	 *   Array describing the page.
	 *
	 *   @type string      id           Id to reference the page.
	 *   @type string      title        Page title. Used in menus and breadcrumbs.
	 *   @type string|null parent       Parent ID. Null for new top level page.
	 *   @type string      path         Path for this page, full path in app context; ex /analytics/report
	 *   @type string      capability   Capability needed to access the page.
	 *   @type string      icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
	 *   @type int         position     Menu item position.
	 * }
	 */
	public function register_page( $options ) {
		$defaults = array(
			'id'         => null,
			'parent'     => null,
			'title'      => '',
			'capability' => 'manage_options',
			'path'       => '',
			'icon'       => '',
			'position'   => null,
			'js_page'    => true,
		);

		$options = wp_parse_args( $options, $defaults );

		if ( 0 !== strpos( $options['path'], self::PAGE_ROOT ) ) {
			$options['path'] = self::PAGE_ROOT . '#/' . $options['path'];
		}
		if ( is_null( $options['parent'] ) ) {
			add_menu_page(
				$options['title'],
				$options['title'],
				$options['capability'],
				$options['path'],
				array( __CLASS__, 'page_wrapper' ),
				$options['icon'],
				$options['position']
			);
		} else {
			$parent_path = $this->get_path_from_id( $options['parent'] );
			add_submenu_page(
				$parent_path,
				$options['title'],
				$options['title'],
				$options['capability'],
				$options['path'],
				array( __CLASS__, 'page_wrapper' )
			);
		}
		$this->connect_page( $options );
	}

	/**
	 * Set up a div for the app to render into.
	 */
	public static function page_wrapper() {
		?>
		<div class="wrap eaccounting">
			<div id="eaccounting"></div>
		</div>
		<?php
	}
}
