<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Menus.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Menus extends \EverAccounting\Singleton {

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( __CLASS__, 'main_menu' ), 1 );
		add_action( 'admin_menu', array( __CLASS__, 'items_menu' ), 20 );
		add_action( 'admin_menu', array( __CLASS__, 'sales_menu' ), 30 );
		add_action( 'admin_menu', array( __CLASS__, 'purchases_menu' ), 40 );
		add_action( 'admin_menu', array( __CLASS__, 'banking_menu' ), 50 );
		add_action( 'admin_menu', array( __CLASS__, 'reports_menu' ), 600 );
		add_action( 'admin_menu', array( __CLASS__, 'tools_menu' ), 700 );
		add_action( 'admin_menu', array( __CLASS__, 'settings_menu' ), 999 );
		//add_action( 'admin_menu', array( __CLASS__, 'extensions_menu' ), 9999 );

		add_action( 'in_admin_header', array( __CLASS__, 'in_admin_header' ) );
	}

	/**
	 * Add main menu.
	 *
	 * @since 1.0.0
	 */
	public static function main_menu() {
		global $menu;

		if ( current_user_can( 'manage_eaccounting' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"><path style=" stroke:none;fill-rule:nonzero;fill:rgb(93.333333%,93.333333%,93.333333%);fill-opacity:1;" d="M 18 1.609375 C 14.292969 -0.539062 9.707031 -0.539062 6 1.609375 C 2.292969 3.757812 0 7.714844 0 12 C 0 16.285156 2.292969 20.242188 6 22.390625 C 9.707031 24.539062 14.292969 24.539062 18 22.390625 C 21.707031 20.242188 24 16.285156 24 12 C 24 7.714844 21.707031 3.757812 18 1.609375 Z M 18.371094 13.390625 L 17.496094 18.070312 C 17.339844 18.898438 16.621094 19.488281 15.78125 19.488281 L 14.664062 19.488281 C 14.832031 18.625 15 17.761719 15.167969 16.894531 C 13.738281 18.347656 11.039062 19.691406 8.964844 19.691406 C 7.65625 19.691406 6.574219 19.222656 5.722656 18.300781 C 4.871094 17.375 4.441406 16.199219 4.441406 14.785156 C 4.441406 12.898438 5.125 11.230469 6.480469 9.78125 C 6.527344 9.730469 6.574219 9.683594 6.625 9.636719 C 7.980469 8.257812 9.996094 7.273438 11.964844 7.667969 C 13.65625 8.003906 14.914062 9.457031 15.3125 11.089844 L 15.371094 11.292969 C 15.503906 11.84375 15.203125 12.324219 14.652344 12.46875 L 8.484375 14.039062 L 8.484375 13.164062 L 13.90625 11.304688 C 13.824219 11.136719 13.726562 10.96875 13.609375 10.800781 C 13.019531 9.984375 12.226562 9.574219 11.242188 9.574219 C 10.019531 9.574219 8.941406 10.078125 8.039062 11.074219 C 7.714844 11.4375 7.453125 11.808594 7.246094 12.203125 C 7.007812 12.660156 6.851562 13.140625 6.757812 13.644531 C 6.707031 13.945312 6.671875 14.257812 6.671875 14.578125 C 6.671875 15.492188 6.960938 16.246094 7.523438 16.835938 C 8.101562 17.425781 8.832031 17.6875 9.71875 17.710938 C 10.765625 17.746094 12.238281 17.328125 13.296875 16.777344 C 13.894531 16.464844 14.605469 16.5 15.15625 16.882812 L 15.167969 16.894531 C 15.179688 16.824219 15.191406 16.753906 15.214844 16.679688 C 15.503906 15.191406 15.792969 13.714844 16.078125 12.226562 C 16.378906 10.65625 16.65625 9.109375 15.816406 7.65625 C 15.144531 6.46875 13.859375 5.855469 12.527344 5.773438 C 11.460938 5.699219 10.367188 5.929688 9.382812 6.335938 C 9.300781 6.371094 8.460938 6.757812 8.460938 6.769531 C 8.460938 6.769531 7.609375 5.257812 7.609375 5.257812 C 7.570312 5.171875 9.144531 4.5 9.289062 4.453125 C 9.898438 4.222656 10.523438 4.042969 11.160156 3.9375 C 12.421875 3.707031 13.738281 3.730469 14.976562 4.09375 C 16.621094 4.585938 18.011719 5.84375 18.515625 7.5 C 19.105469 9.382812 18.636719 11.878906 18.371094 13.390625 Z M 18.371094 13.390625 "/></svg>' );
		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_accounting',
			'ever-accounting',
			null,
			$icon,
			'54.5'
		);

		add_submenu_page(
			'ever-accounting',
			__( 'Overview', 'wp-ever-accounting' ),
			__( 'Overview', 'wp-ever-accounting' ),
			'manage_accounting',
			'ever-accounting',
			array( Overview::class, 'output' )
		);
	}

	/**
	 * Add Items menu.
	 *
	 * @since 1.0.0
	 */
	public static function items_menu() {
		$tabs = eac_get_items_tabs();
		if ( empty( $tabs ) ) {
			return;
		}
		add_submenu_page(
			'ever-accounting',
			__( 'Items', 'wp-ever-accounting' ),
			__( 'Items', 'wp-ever-accounting' ),
			'manage_options',
			'eac-items',
			array( Items::class, 'output' )
		);
	}

	/**
	 * Add Sales menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function sales_menu() {
		$tabs = eac_get_sales_tabs();
		if ( empty( $tabs ) ) {
			return;
		}

		add_submenu_page(
			'ever-accounting',
			__( 'Sales', 'wp-ever-accounting' ),
			__( 'Sales', 'wp-ever-accounting' ),
			'manage_options',
			'eac-sales',
			array( Sales::class, 'output' )
		);
	}

	/**
	 * Add purchase menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function purchases_menu() {
		$tabs = eac_get_purchase_tabs();
		if ( empty( $tabs ) ) {
			return;
		}

		add_submenu_page(
			'ever-accounting',
			__( 'Purchases', 'wp-ever-accounting' ),
			__( 'Purchases', 'wp-ever-accounting' ),
			'manage_options',
			'eac-purchases',
			array( Purchases::class, 'output' )
		);
	}

	/**
	 * Add Banking menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function banking_menu() {
		$tabs = eac_get_banking_tabs();
		if ( empty( $tabs ) ) {
			return;
		}

		add_submenu_page(
			'ever-accounting',
			__( 'Banking', 'wp-ever-accounting' ),
			__( 'Banking', 'wp-ever-accounting' ),
			'manage_options',
			'eac-banking',
			array( Banking::class, 'output' )
		);
	}

	/**
	 * Add Tools menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function tools_menu() {
		// $tabs = eac_get_tools_tabs();
		// if ( empty( $tabs ) ) {
		// return;
		// }

		add_submenu_page(
			'ever-accounting',
			__( 'Tools', 'wp-ever-accounting' ),
			__( 'Tools', 'wp-ever-accounting' ),
			'manage_options',
			'eac-tools',
			array( Tools::class, 'output' )
		);
	}

	/**
	 * Add Reports menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function reports_menu() {
		$tabs = eac_get_reports_tabs();
		if ( empty( $tabs ) ) {
			return;
		}

		add_submenu_page(
			'ever-accounting',
			__( 'Reports', 'wp-ever-accounting' ),
			__( 'Reports', 'wp-ever-accounting' ),
			'manage_options',
			'eac-reports',
			array( Reports::class, 'output' )
		);
	}

	/**
	 * Add settings menu.
	 *
	 * @since 1.0.0
	 */
	public static function settings_menu() {
		$hook = add_submenu_page(
			'ever-accounting',
			__( 'Settings', 'wp-ever-accounting' ),
			__( 'Settings', 'wp-ever-accounting' ),
			'manage_options',
			'eac-settings',
			array( Settings::class, 'output' )
		);

		add_action( 'load-' . $hook, array( __CLASS__, 'settings_page_init' ) );
	}

	/**
	 * Settings page init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function settings_page_init() {
		Settings::get_tabs();
	}

	/**
	 * Add Extensions menu.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function extensions_menu() {
		add_submenu_page(
			'ever-accounting',
			__( 'Extensions', 'wp-ever-accounting' ),
			__( 'Extensions', 'wp-ever-accounting' ),
			'manage_options',
			'eac-extensions',
			array( Extensions::class, 'output' )
		);
	}

	/**
	 * Plugin header.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function in_admin_header() {
		if ( ! eac_is_admin_page() ) {
			return;
		}
		$menus = array(
			array(
				'title' => __( 'Dashboard', 'wp-ever-accounting' ),
				'icon'  => 'dashicons dashicons-dashboard',
				'url'   => admin_url( 'admin.php?page=ever-accounting' ),
			),
			array(
				'title' => __( 'Sales', 'wp-ever-accounting' ),
				'icon'  => 'dashicons dashicons-money-alt',
				'url'   => admin_url( 'admin.php?page=eac-sales' ),
			),
			array(
				'title' => __( 'Purchases', 'wp-ever-accounting' ),
				'icon'  => 'dashicons dashicons-cart',
				'url'   => admin_url( 'admin.php?page=eac-purchases' ),
			),
			array(
				'title'   => __( 'New', 'wp-ever-accounting' ),
				'icon'    => 'dashicons dashicons-plus-alt',
				'url'     => '#',
				'submenu' => array(
					array(
						'title' => __( 'Payment', 'wp-ever-accounting' ),
						'url'   => eac_action_url( 'action=get_html_response&html_type=edit_payment' ),
					),
					array(
						'title' => __( 'Expense', 'wp-ever-accounting' ),
						'url'   => eac_action_url( 'action=get_html_response&html_type=edit_expense' ),
					),
					array(
						'title' => __( 'Item', 'wp-ever-accounting' ),
						'url'   => eac_action_url( 'action=get_html_response&html_type=edit_item' ),
					),
					array(
						'title' => __( 'Invoice', 'wp-ever-accounting' ),
						'url'   => admin_url( 'admin.php?page=eac-sales&tab=invoices&action=add' ),
					),
					array(
						'title' => __( 'Customer', 'wp-ever-accounting' ),
						'url'   => eac_action_url( 'action=get_html_response&html_type=edit_customer' ),
					),
					array(
						'title' => __( 'Vendor', 'wp-ever-accounting' ),
						'url'   => eac_action_url( 'action=get_html_response&html_type=edit_vendor' ),
					),
				),
			),
			array(
				'title' => __( 'Support', 'wp-ever-accounting' ),
				'icon'  => 'dashicons dashicons-editor-help',
				'url'   => 'https://wpeveraccounting.com/docs/',
			),
		);
		?>
		<div class="eac-admin-header">
			<div class="eac-admin-header__wrapper">
				<div class="eac-admin-header__logo">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=ever-accounting' ) ); ?>">
						<?php echo eac_get_svg_icon( 'logo', 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
				</div>
				<h1 class="eac-admin-header__title"><?php esc_html_e( 'Ever Accounting', 'wp-ever-accounting' ); ?></h1>
				<?php if ( ! empty( $menus ) ) : ?>
					<ul class="eac-admin-header__menu">
						<?php foreach ( $menus as $menu ) : ?>
							<li>
								<a href="<?php echo esc_url( $menu['url'] ); ?>">
									<?php if ( ! empty( $menu['icon'] ) ) : ?>
										<i class="eac-admin-header__menu-icon <?php echo esc_attr( $menu['icon'] ); ?>"></i>
									<?php endif; ?>
									<?php if ( ! empty( $menu['title'] ) ) : ?>
										<?php echo esc_html( $menu['title'] ); ?>
									<?php endif; ?>
								</a>
								<?php if ( ! empty( $menu['submenu'] ) ) : ?>
									<ul>
										<?php foreach ( $menu['submenu'] as $submenu ) : ?>
											<li><a href="<?php echo esc_url( $submenu['url'] ); ?>"><?php echo esc_html( $submenu['title'] ); ?></a></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
