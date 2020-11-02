<?php
/**
 * Admin Items Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Items
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();
$item_id = isset( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : null;
try {
	$item = new \EverAccounting\Item( $item_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $item->exists() ? __( 'Update Item', 'wp-ever-accounting' ) : __( 'Add Item', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-item-form" class="ea-ajax-form" method="post" enctype="multipart/form-data">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter name', 'wp-ever-accounting' ),
						'value'         => $item->get_name(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Sku', 'wp-ever-accounting' ),
						'name'          => 'sku',
						'placeholder'   => __( 'Enter Sku', 'wp-ever-accounting' ),
						'value'         => $item->get_sku(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Purchase price', 'wp-ever-accounting' ),
						'name'          => 'purchase_price',
						'placeholder'   => __( 'Enter Purchase price', 'wp-ever-accounting' ),
						'value'         => $item->get_purchase_price(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Sale price', 'wp-ever-accounting' ),
						'name'          => 'sale_price',
						'placeholder'   => __( 'Enter Sale price', 'wp-ever-accounting' ),
						'value'         => $item->get_sale_price(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Quantity', 'wp-ever-accounting' ),
						'name'          => 'quantity',
						'placeholder'   => __( 'Enter Quantity', 'wp-ever-accounting' ),
						'value'         => $item->get_quantity(),
					)
				);
				eaccounting_category_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $item->get_category_id(),
						'type'          => 'item',
						'creatable'     => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Tax id', 'wp-ever-accounting' ),
						'name'          => 'tax_id',
						'placeholder'   => __( 'Enter Tax Id', 'wp-ever-accounting' ),
						'value'         => $item->get_tax_id(),
					)
				);
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'placeholder'   => __( 'Enter Description', 'wp-ever-accounting' ),
						'value'         => $item->get_description(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $item->get_id(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_item',
					)
				);

				?>
			</div>
			<?php
				wp_nonce_field( 'ea_edit_item' );
				submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>
</div>

