<?php
/**
 * Admin Item Edit Page.
 * Page: Items
 * Tab: Items
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $item_id
 */

defined( 'ABSPATH' ) || exit();

try {
	$item = new \EverAccounting\Models\Item( $item_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$back_url = remove_query_arg( array( 'action', 'item_id' ) );
$title = $item->exists() ? __( 'Update Item', 'wp-ever-accounting' ) : __( 'Add Item', 'wp-ever-accounting' );
?>

<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php echo $title; ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><?php _e( 'All Items', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card__inside">
		<form id="ea-item-form" method="post" enctype="multipart/form-data">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
						'value'         => $item->get_name(),
						'required'      => true,
					)
				);
				eaccounting_category_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $item->get_category_id(),
						'required'      => false,
						'type'          => 'item',
						'creatable'     => true,
						'ajax_action'   => 'eaccounting_get_item_categories',
						'modal_id'      => 'ea-modal-add-item-category',
					)
				);
				//				eaccounting_text_input(
				//					array(
				//						'wrapper_class' => 'ea-col-6',
				//						'label'         => __( 'Quantity', 'wp-ever-accounting' ),
				//						'name'          => 'quantity',
				//						'placeholder'   => __( 'Enter Quantity', 'wp-ever-accounting' ),
				//						'value'         => $item->get_quantity(),
				//						'required'      => true,
				//					)
				//				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Sale price', 'wp-ever-accounting' ),
						'name'          => 'sale_price',
						'data_type'     => 'price',
						'placeholder'   => __( 'Enter Sale price', 'wp-ever-accounting' ),
						'value'         => $item->get_sale_price(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Purchase price', 'wp-ever-accounting' ),
						'name'          => 'purchase_price',
						'data_type'     => 'price',
						'placeholder'   => __( 'Enter Purchase price', 'wp-ever-accounting' ),
						'value'         => $item->get_purchase_price(),
						'required'      => true,
					)
				);
				if ( eaccounting_tax_enabled() ) :
					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Sales Tax (%)', 'wp-ever-accounting' ),
							'name'          => 'sales_tax_rate',
							'placeholder'   => __( 'Enter Sale price', 'wp-ever-accounting' ),
							'value'         => $item->get_sales_tax_rate(),
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Purchase Tax (%)', 'wp-ever-accounting' ),
							'name'          => 'purchase_tax_rate',
							'placeholder'   => __( 'Enter Purchase price', 'wp-ever-accounting' ),
							'value'         => $item->get_purchase_tax_rate(),
						)
					);
				endif;
				eaccounting_textarea(
					array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $item->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
					)
				);

				eaccounting_file_input(
					array(
						'label'         => __( 'Product Image', 'wp-ever-accounting' ),
						'name'          => 'image_id',
						'value'         => $item->get_attachment(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Upload Image', 'wp-ever-accounting' ),
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
