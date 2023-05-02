<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Items.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Items extends \EverAccounting\Singleton {

	/**
	 * Items constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_items_tab_items', array( __CLASS__, 'output_items_tab' ) );
		add_action( 'admin_footer', array( __CLASS__, 'output_item_modal' ) );
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_items_tabs();
		$tab          = eac_filter_input( INPUT_GET, 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_filter_input( INPUT_GET, 'page' );
		$page_name    = 'items';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output accounts tab.
	 *
	 * @since 1.1.0
	 */
	public static function output_items_tab() {
		$action  = eac_filter_input( INPUT_GET, 'action' );
		$item_id = eac_filter_input( INPUT_GET, 'item_id', 'absint' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/items/edit-item.php';
		} else {
			include dirname( __FILE__ ) . '/views/items/list-items.php';
		}
	}

	/**
	 * Output the item modal.
	 *
	 * @since 1.0.0
	 */
	public static function output_item_modal() {
		$item = new \EverAccounting\Models\Item();
		?>
		<script type="text/template" id="eac-item-modal" data-title="<?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?>">
			<?php require __DIR__ . '/views/items/item-form.php'; ?>
		</script>
		<?php
	}
}
