<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package StarterPlugin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'buffer_start' ), 1 );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( $this, 'update_footer' ), PHP_INT_MAX );
		add_action( 'in_admin_header', array( __CLASS__, 'in_admin_header' ) );
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );
	}

	/**
	 * Start output buffering.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function buffer_start() {
		ob_start();
	}

	/**
	 * Add body class.
	 *
	 * @param string $classes Body classes.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$classes .= ' eac-admin';
		}

		return $classes;
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) ) {
			return;
		}

		// 3rd party scripts.
		$scripts = EAC()->scripts;
		$scripts->enqueue_style( 'bytekit-core' );
		$scripts->register_style( 'eac-select2', 'css/select-woo.css' );
		$scripts->register_script( 'eac-select2', 'js/select-woo.js', array( 'jquery' ) );
		$scripts->register_style( 'eac-jquery-ui', 'css/jquery-ui.css' );
		$scripts->register_script( 'eac-tiptip', 'js/tipTip.js', array( 'jquery' ) );
		$scripts->register_script( 'eac-blockui', 'js/blockui.js', array( 'jquery' ) );
		$scripts->register_script( 'eac-mask', 'js/mask.js', array( 'jquery' ) );
		$scripts->register_script( 'eac-inputmask', 'js/inputmask.js', array( 'jquery' ) );
		$scripts->register_script( 'eac-modal', 'js/micromodal.js' );
		$scripts->register_script( 'eac-chartjs', 'js/chartjs.js' );

		// Core scripts.
		$admin_js_deps  = array( 'jquery', 'wp-util', 'eac-select2', 'jquery-ui-datepicker', 'jquery-ui-tooltip', 'eac-blockui', 'eac-modal' );
		$admin_css_deps = array( 'eac-jquery-ui', 'eac-select2' );
		$scripts->register_script( 'eac-admin', 'js/eac-admin.js', $admin_js_deps );
		$scripts->register_script( 'eac-settings', 'js/eac-settings.js', array( 'eac-admin' ) );
		$scripts->register_style( 'eac-admin', 'css/eac-admin.css', $admin_css_deps );
		$scripts->register_style( 'eac-settings', 'css/eac-settings.css', array( 'eac-jquery-ui' ) );
		$scripts->enqueue_script( 'eac-admin' );
		$scripts->enqueue_style( 'eac-admin' );
		// $scripts->enqueue_script( 'eac-alpine' );

		// if settings page.
		if ( 'ever-accounting_page_eac-settings' === $hook ) {
			$scripts->register_script( 'eac-settings', 'js/settings.js', array( 'jquery' ) );
			$scripts->enqueue_style( 'eac-settings' );
		}

		// if dashboard or reports page.

		if ( 'toplevel_page_ever-accounting' === $hook || 'ever-accounting_page_eac-reports' === $hook ) {
			$scripts->enqueue_script( 'eac-chartjs' );
		}

		wp_localize_script(
			'eac-admin',
			'eac_admin_js_vars',
			array(
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'search_nonce'   => wp_create_nonce( 'eac_search_action' ),
				'currency_nonce' => wp_create_nonce( 'eac_currency' ),
				'account_nonce'  => wp_create_nonce( 'eac_account' ),
				'item_nonce'     => wp_create_nonce( 'eac_item' ),
				'customer_nonce' => wp_create_nonce( 'eac_customer' ),
				'vendor_nonce'   => wp_create_nonce( 'eac_vendor' ),
				'payment_nonce'  => wp_create_nonce( 'eac_payment' ),
				'expense_nonce'  => wp_create_nonce( 'eac_expense' ),
				'invoice_nonce'  => wp_create_nonce( 'eac_invoice' ),
				'purchase_nonce' => wp_create_nonce( 'eac_purchase' ),

				'i18n'           => array(
					'confirm_delete' => __( 'Are you sure you want to delete this item?', 'wp-ever-accounting' ),
				),
			)
		);
	}

	/**
	 * Request review.
	 *
	 * @param string $text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function admin_footer_text( $text ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$text = sprintf(
			/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wp-ever-accounting' ),
				'<strong>' . esc_html( EAC()->get_name() ) . '</strong>',
			);
			if ( EAC()->review_url ) {
				$text .= sprintf(
				/* translators: %s: Plugin name */
					__( ' Share your appreciation with a five-star review %s.', 'wp-ever-accounting' ),
					'<a href="' . esc_url( EAC()->review_url ) . '" target="_blank">here</a>'
				);
			}
		}

		return $text;
	}

	/**
	 * Update footer.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function update_footer( $footer_text ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$footer_text = sprintf( esc_html__( 'Version %s', 'wp-ever-accounting' ), EAC()->get_version() );
		}

		return $footer_text;
	}

	/**
	 * Plugin header.
	 *
	 * @since 1.0.0
	 * @returns void
	 */
	public static function in_admin_header() {
		if ( ! in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
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
						'url'   => '#',
					),
					array(
						'title' => __( 'Expense', 'wp-ever-accounting' ),
						'url'   => '#',
					),
					array(
						'title' => __( 'Item', 'wp-ever-accounting' ),
						'url'   => '#',
					),
					array(
						'title' => __( 'Invoice', 'wp-ever-accounting' ),
						'url'   => '#',
					),
					array(
						'title' => __( 'Customer', 'wp-ever-accounting' ),
						'url'   => '#',
					),
					array(
						'title' => __( 'Vendor', 'wp-ever-accounting' ),
						'url'   => '#',
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
						<svg class="svg-icon" width="40" height="40" aria-hidden="true" role="img" focusable="false" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M 18 1.609375 C 14.292969 -0.539062 9.707031 -0.539062 6 1.609375 C 2.292969 3.757812 0 7.714844 0 12 C 0 16.285156 2.292969 20.242188 6 22.390625 C 9.707031 24.539062 14.292969 24.539062 18 22.390625 C 21.707031 20.242188 24 16.285156 24 12 C 24 7.714844 21.707031 3.757812 18 1.609375 Z M 18.371094 13.390625 L 17.496094 18.070312 C 17.339844 18.898438 16.621094 19.488281 15.78125 19.488281 L 14.664062 19.488281 L 15.167969 16.894531 C 13.738281 18.347656 11.039062 19.691406 8.964844 19.691406 C 7.65625 19.691406 6.574219 19.222656 5.722656 18.300781 C 4.871094 17.375 4.441406 16.199219 4.441406 14.785156 C 4.441406 12.898438 5.125 11.230469 6.480469 9.78125 L 6.625 9.636719 C 7.980469 8.257812 9.996094 7.273438 11.964844 7.667969 C 13.65625 8.003906 14.914062 9.457031 15.3125 11.089844 L 15.371094 11.292969 C 15.503906 11.84375 15.203125 12.324219 14.652344 12.46875 L 8.484375 14.039062 L 8.484375 13.164062 L 13.90625 11.304688 C 13.824219 11.136719 13.726562 10.96875 13.609375 10.800781 C 13.019531 9.984375 12.226562 9.574219 11.242188 9.574219 C 10.019531 9.574219 8.941406 10.078125 8.039062 11.074219 C 7.714844 11.4375 7.453125 11.808594 7.246094 12.203125 C 7.007812 12.660156 6.851562 13.140625 6.757812 13.644531 C 6.707031 13.945312 6.671875 14.257812 6.671875 14.578125 C 6.671875 15.492188 6.960938 16.246094 7.523438 16.835938 C 8.101562 17.425781 8.832031 17.6875 9.71875 17.710938 C 10.765625 17.746094 12.238281 17.328125 13.296875 16.777344 C 13.894531 16.464844 14.605469 16.5 15.15625 16.882812 L 15.167969 16.894531 C 15.179688 16.824219 15.191406 16.753906 15.214844 16.679688 C 15.503906 15.191406 15.792969 13.714844 16.078125 12.226562 C 16.378906 10.65625 16.65625 9.109375 15.816406 7.65625 C 15.144531 6.46875 13.859375 5.855469 12.527344 5.773438 C 11.460938 5.699219 10.367188 5.929688 9.382812 6.335938 C 9.300781 6.371094 8.460938 6.757812 8.460938 6.769531 L 7.609375 5.257812 C 7.570312 5.171875 9.144531 4.5 9.289062 4.453125 C 9.898438 4.222656 10.523438 4.042969 11.160156 3.9375 C 12.421875 3.707031 13.738281 3.730469 14.976562 4.09375 C 16.621094 4.585938 18.011719 5.84375 18.515625 7.5 C 19.105469 9.382812 18.636719 11.878906 18.371094 13.390625 Z M 18.371094 13.390625" fill="currentColor"/></svg>
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

	/**
	 * Save settings.
	 *
	 * @since 1.1.6
	 */
	public function save_settings() {
		global $current_tab, $current_section;
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// We should only save on the settings page.
		if ( ! is_admin() || empty( $page ) || 'eac-settings' !== $page ) {
			return;
		}

		// Include settings pages.
		Settings::get_tabs();

		// Get current tab/section.
		$current_tab     = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';
		$current_section = filter_input( INPUT_GET, 'section', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';
		$is_save         = isset( $_POST['save'] ) ? true : false;

		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}_{$current_section}", $is_save ) ) {
			Settings::save();
		} elseif ( '' === $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}", $is_save ) ) {
			Settings::save();
		}
	}
}