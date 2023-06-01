<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Misc.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Misc extends \EverAccounting\Singleton {

	/**
	 * Misc constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_filter( 'ever_accounting_settings_tabs_array', array( __CLASS__, 'add_settings_tabs' ) );
		add_action( 'ever_accounting_settings_tab_currencies', array( __CLASS__, 'output_currencies_tab' ) );
		add_action( 'ever_accounting_settings_tab_categories', array( __CLASS__, 'output_categories_tab' ) );
		add_action( 'ever_accounting_settings_tab_tax', array( __CLASS__, 'output_tax_tab' ) );
	}

	/**
	 * Add the settings tab.
	 *
	 * @param array $tabs Settings tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function add_settings_tabs( $tabs ) {
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );
		$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );
		return $tabs;
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
	}

	/**
	 * Output the currencies tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_currencies_tab() {
		$action = eac_get_input_var( 'action' );
		$code   = eac_get_input_var( 'currency' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/currencies/edit-currency.php';
		} else {
			include dirname( __FILE__ ) . '/views/currencies/list-currencies.php';
		}
	}

	/**
	 * Output the categories tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_categories_tab() {
		$action      = eac_get_input_var( 'action' );
		$category_id = eac_get_input_var( 'category_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/categories/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/categories/list-categories.php';
		}
	}

	/**
	 * Output the category modal.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_category_modal() {
		$category = new \EverAccounting\Models\Category();
		?>
		<script type="text/template" id="eac-category-modal" data-title="<?php esc_html_e( 'Add Category', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/categories/category-form.php'; ?>
		</script>
		<?php
	}

	/**
	 * Output the tax tab.
	 *
	 * @param string $section Current section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_tax_tab( $section ) {
		if ( 'taxes' !== $section ) {
			return;
		}
		$action = eac_get_input_var( 'action' );
		$tax_id = eac_get_input_var( 'tax_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/taxes/edit-tax.php';
		} else {
			include dirname( __FILE__ ) . '/views/taxes/list-taxes.php';
		}
	}
}
