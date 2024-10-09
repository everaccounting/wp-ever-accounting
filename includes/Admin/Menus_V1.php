<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Menus class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Menus_V1 {

	/**
	 * Main menu slug.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	const PARENT_SLUG = 'ever-accounting';

	/**
	 * Current page without 'eac-' prefix.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	public $page = '';

	/**
	 * Current page tabs.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	public $tabs = array();

	/**
	 * Current page tab.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	public $tab = '';

	/**
	 * Current actions.
	 *
	 * @since 3.0.0
	 * @var array
	 */
	public $actions = array();

	/**
	 * Current action.
	 *
	 * @since 3.0.0
	 * @var string
	 */
	public $action = '';

	/**
	 * Menus constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 3.0.0
	 */
	public function admin_menu() {
		global $menu, $admin_page_hooks;
		if ( current_user_can( 'manage_options' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"><path style=" stroke:none;fill-rule:nonzero;fill:rgb(93.333333%,93.333333%,93.333333%);fill-opacity:1;" d="M 18 1.609375 C 14.292969 -0.539062 9.707031 -0.539062 6 1.609375 C 2.292969 3.757812 0 7.714844 0 12 C 0 16.285156 2.292969 20.242188 6 22.390625 C 9.707031 24.539062 14.292969 24.539062 18 22.390625 C 21.707031 20.242188 24 16.285156 24 12 C 24 7.714844 21.707031 3.757812 18 1.609375 Z M 18.371094 13.390625 L 17.496094 18.070312 C 17.339844 18.898438 16.621094 19.488281 15.78125 19.488281 L 14.664062 19.488281 C 14.832031 18.625 15 17.761719 15.167969 16.894531 C 13.738281 18.347656 11.039062 19.691406 8.964844 19.691406 C 7.65625 19.691406 6.574219 19.222656 5.722656 18.300781 C 4.871094 17.375 4.441406 16.199219 4.441406 14.785156 C 4.441406 12.898438 5.125 11.230469 6.480469 9.78125 C 6.527344 9.730469 6.574219 9.683594 6.625 9.636719 C 7.980469 8.257812 9.996094 7.273438 11.964844 7.667969 C 13.65625 8.003906 14.914062 9.457031 15.3125 11.089844 L 15.371094 11.292969 C 15.503906 11.84375 15.203125 12.324219 14.652344 12.46875 L 8.484375 14.039062 L 8.484375 13.164062 L 13.90625 11.304688 C 13.824219 11.136719 13.726562 10.96875 13.609375 10.800781 C 13.019531 9.984375 12.226562 9.574219 11.242188 9.574219 C 10.019531 9.574219 8.941406 10.078125 8.039062 11.074219 C 7.714844 11.4375 7.453125 11.808594 7.246094 12.203125 C 7.007812 12.660156 6.851562 13.140625 6.757812 13.644531 C 6.707031 13.945312 6.671875 14.257812 6.671875 14.578125 C 6.671875 15.492188 6.960938 16.246094 7.523438 16.835938 C 8.101562 17.425781 8.832031 17.6875 9.71875 17.710938 C 10.765625 17.746094 12.238281 17.328125 13.296875 16.777344 C 13.894531 16.464844 14.605469 16.5 15.15625 16.882812 L 15.167969 16.894531 C 15.179688 16.824219 15.191406 16.753906 15.214844 16.679688 C 15.503906 15.191406 15.792969 13.714844 16.078125 12.226562 C 16.378906 10.65625 16.65625 9.109375 15.816406 7.65625 C 15.144531 6.46875 13.859375 5.855469 12.527344 5.773438 C 11.460938 5.699219 10.367188 5.929688 9.382812 6.335938 C 9.300781 6.371094 8.460938 6.757812 8.460938 6.769531 C 8.460938 6.769531 7.609375 5.257812 7.609375 5.257812 C 7.570312 5.171875 9.144531 4.5 9.289062 4.453125 C 9.898438 4.222656 10.523438 4.042969 11.160156 3.9375 C 12.421875 3.707031 13.738281 3.730469 14.976562 4.09375 C 16.621094 4.585938 18.011719 5.84375 18.515625 7.5 C 19.105469 9.382812 18.636719 11.878906 18.371094 13.390625 Z M 18.371094 13.390625 "/></svg>' ); // phpcs:ignore

		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_options',
			self::PARENT_SLUG,
			null,
			$icon,
			'54.5'
		);
		$admin_page_hooks['ever-accounting'] = 'ever-accounting';

		// register dashboard.
		$this->register_menu(
			array(
				'menu_title' => __( 'Dashboard', 'wp-ever-accounting' ),
				'page_title' => __( 'Dashboard', 'wp-ever-accounting' ),
				'capability' => 'manage_options',
				'menu_slug'  => self::PARENT_SLUG,
			)
		);

		$submenus = Utilities::get_menus();
		usort(
			$submenus,
			function ( $a, $b ) {
				$a = isset( $a['position'] ) ? $a['position'] : 10;
				$b = isset( $b['position'] ) ? $b['position'] : 10;

				return $a - $b;
			}
		);

		foreach ( $submenus as $submenu ) {
			$this->register_menu( $submenu );
		}
	}

	/**
	 * Register admin menu.
	 *
	 * @param array $menu Menu data.
	 *
	 * @since 3.0.0
	 */
	public function register_menu( $menu ) {
		global $plugin_page, $pagenow;

		$menu = wp_parse_args(
			$menu,
			array(
				'parent'     => self::PARENT_SLUG,
				'menu_title' => '',
				'page_title' => '',
				'capability' => 'manage_options',
				'menu_slug'  => '',
				'callback'   => array( $this, 'output' ),
				'tabs'       => array(),
			)
		);

		$load = add_submenu_page(
			$menu['parent'],
			$menu['page_title'],
			$menu['menu_title'],
			$menu['capability'],
			$menu['menu_slug'],
			$menu['callback']
		);

		// Not on this page?
		if ( empty( $plugin_page ) || 'admin.php' !== $pagenow || $plugin_page !== $menu['menu_slug'] ) {
			return;
		}

		// setup vars.
		$tab        = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
		$action     = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
		$page       = preg_replace( '/^.*?eac-/', '', $menu['menu_slug'] );
		$this->page = self::PARENT_SLUG === $page ? 'dashboard' : $page;

		/**
		 * Fires when the page is initialized.
		 *
		 * @param string $page The current page.
		 * @param string $tab The current tab.
		 * @param string $action The current action.
		 */
		do_action( 'eac_' . $this->page . '_page_init', $this->tab, $this->action );

		$this->actions = apply_filters( 'eac_' . $this->page . '_page_actions', array( 'add', 'edit', 'view' ) );
		$this->tabs    = apply_filters( 'eac_' . $this->page . '_page_tabs', $menu['tabs'] );
		$this->tab     = ! empty( $tab ) && array_key_exists( $tab, $this->tabs ) ? sanitize_key( $tab ) : current( array_keys( $this->tabs ) );
		$this->action  = ! empty( $action ) && in_array( $action, $this->actions, true ) ? sanitize_key( $action ) : '';

		// if the tab is not valid, redirect remove the tab query arg.
		if ( $this->tabs && $tab && ! array_key_exists( $tab, $this->tabs ) ) {
			wp_safe_redirect( remove_query_arg( 'tab' ) );
			exit;
		}

		add_filter( 'admin_title', array( $this, 'admin_title' ) );
		add_action( 'load-' . $load, array( $this, 'handle_page_load' ) );
	}

	/**
	 * Set page title.
	 *
	 * @param string $title Page title.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function admin_title( $title ) {
		if ( ! empty( $this->tab ) ) {
			$title = sprintf( '%s - %s', $this->tabs[ $this->tab ], $title );
		}

		return $title;
	}

	/**
	 * Handle page load.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function handle_page_load() {
		if ( ! empty( $this->page ) && ! empty( $this->tab ) && ! empty( $this->action ) && has_action( 'eac_' . $this->page . '_page_' . $this->tab . '_' . $this->action ) ) {
			/**
			 * Fires when the page is loaded.
			 *
			 * @since 3.0.0
			 */
			do_action( 'load_eac_' . $this->page . '_page_' . $this->tab . '_' . $this->action );

		} elseif ( ! empty( $this->page ) && ! empty( $this->tab ) && has_action( 'eac_' . $this->page . '_page_' . $this->tab ) ) {
			/**
			 * Fires when the page is loaded.
			 *
			 * @param string $action The current action.
			 *
			 * @since 3.0.0
			 */
			do_action( 'load_eac_' . $this->page . '_page_' . $this->tab, $this->action );
		}

		/**
		 * Fires when the page is loaded.
		 *
		 * @param string $tab The current tab.
		 * @param string $action The current action.
		 *
		 * @since 3.0.0
		 */
		do_action( 'load_eac_' . $this->page . '_page', $this->tab, $this->action );
	}

	/**
	 * Output.
	 *
	 * @since 3.0.0
	 */
	public function output() {
		global $plugin_page;
		ob_start();
		?>
		<div class="wrap eac-wrap">
			<?php if ( ! empty( $this->tabs ) ) : ?>
				<nav class="nav-tab-wrapper eac-navbar">
					<?php
					foreach ( $this->tabs as $name => $label ) {
						printf(
							'<a href="%s" class="nav-tab %s">%s</a>',
							esc_url( admin_url( 'admin.php?page=' . $plugin_page . '&tab=' . $name ) ),
							esc_attr( $this->tab === $name ? 'nav-tab-active' : '' ),
							esc_html( $label )
						);
					}
					?>
					<?php
					/**
					 * Fires after the tabs on the settings page.
					 *
					 * @param string $tab Current tab..
					 * @param array  $tabs Tabs.
					 *
					 * @since 1.0.0
					 */
					do_action( 'eac_' . $this->page . '_page_nav_items', $this->tab, $this->tabs );
					?>
				</nav>

				<hr class="wp-header-end">
			<?php endif; ?>

			<?php
			if ( ! empty( $this->page ) && ! empty( $this->tab ) && ! empty( $this->action ) && has_action( 'eac_' . $this->page . '_page_' . $this->tab . '_' . $this->action ) ) {
				/**
				 * Fires before the content on the page.
				 *
				 * @since 1.0.0
				 */
				do_action( 'eac_' . $this->page . '_page_' . $this->tab . '_' . $this->action );

			} elseif ( ! empty( $this->page ) && ! empty( $this->tab ) && has_action( 'eac_' . $this->page . '_page_' . $this->tab ) ) {
				/**
				 * Fires before the content on the page.
				 *
				 * @param string $action The current action.
				 *
				 * @since 1.0.0
				 */
				do_action( 'eac_' . $this->page . '_page_' . $this->tab, $this->action );
			}

			/**
			 * Fires before the content on the page.
			 *
			 * @param string $tab The current tab.
			 * @param string $action The current action.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_' . $this->page . '_page', $this->tab, $this->action );
			?>
		</div>
		<?php
	}
}
