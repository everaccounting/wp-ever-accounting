<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Tax_Settings extends EAccounting_Settings_Page {

	/**
	 * @since 1.0.2
	 * EAccounting_Tax_Settings constructor.
	 */
	public function __construct() {
		$this->id    = 'taxes';
		$this->label = __( 'Tax Rates', 'wp-ever-accounting' );
		add_action( 'eaccounting_admin_field_tax_settings', array( $this, 'tax_page' ) );
		parent::__construct();
	}

	public function get_settings( $section = null ) {
		$settings = array(
			array(
				'type' => 'tax_settings',
			),
		);

		return apply_filters( 'wpcp_get_settings_' . $this->id, $settings );
	}

	public function tax_page(){

		if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_tax' ) {
			eaccounting_get_views( 'misc/edit-tax.php' );
		} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_tax' ) {
			eaccounting_get_views( 'misc/edit-tax.php' );
		} else {
			require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-taxes-list-table.php';
			$list_table = new EAccounting_Taxes_List_Table();

			$action = $list_table->current_action();

			$redirect_to = admin_url( 'admin.php?page=eaccounting-misc&tab=taxes' );

			if ( $action && check_admin_referer( 'bulk-taxes' ) ) {

				$ids = isset( $_GET['tax'] ) ? $_GET['tax'] : false;

				if ( ! is_array( $ids ) ) {
					$ids = array( $ids );
				}
				$ids = array_map( 'intval', $ids );
				foreach ( $ids as $id ) {
					switch ( $action ) {
						case 'activate':
							eaccounting_insert_tax( [ 'id' => $id ] );
							break;
						case 'deactivate':
							eaccounting_insert_tax( [ 'id' => $id ] );
							break;
						case 'delete':
							eaccounting_delete_tax( $id );
							break;
					}
				}

				wp_redirect( $redirect_to );
				exit();
			}

			$list_table->prepare_items();

			?>

			<h1 class="wp-heading-inline"><?php _e( 'Taxes', 'wp-ever-accounting' ); ?></h1>
			<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_tax' ), $redirect_to ) ); ?>"
			   class="page-title-action">
				<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
			<?php do_action( 'eaccounting_taxes_page_top' ); ?>
			<form method="get" action="<?php echo esc_url( $redirect_to ); ?>">
				<div class="ea-list-table">
					<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-taxes' ); ?>
					<input type="hidden" name="page" value="eaccounting-misc"/>
					<input type="hidden" name="tab" value="taxes"/>
					<?php $list_table->views() ?>
					<?php $list_table->display() ?>
				</div>
			</form>
			<?php
			do_action( 'eaccounting_taxes_page_bottom' );
		}
	}
}

return new EAccounting_Tax_Settings();
