<?php
/**
 * Admin Item Edit Page.
 * Page: Items
 * Tab: Items
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     Ever_Accounting
 * @var int $item_id
 */

use \Ever_Accounting\Helpers\Form;
use \Ever_Accounting\Helpers\Tax;
use \Ever_Accounting\Helpers\Price;

defined( 'ABSPATH' ) || exit();

try {
	$item = new \Ever_Accounting\Item( $item_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$title = $item->exists() ? __( 'Update Item', 'wp-ever-accounting' ) : __( 'Add Item', 'wp-ever-accounting' );
?>
	<div class="ea-row">
		<div class="ea-col-7">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Items', 'wp-ever-accounting' ); ?></h1>
			<?php if ( $item->exists() ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'items', 'page' => 'ea-items', 'action' => 'add' ), admin_url( 'admin.php' ) ) );//phpcs:ignore ?>" class="page-title-action">
					<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
				</a>
			<?php else : ?>
				<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
			<?php endif; ?>
		</div>

		<div class="ea-col-5">

		</div>
	</div>
	<hr class="wp-header-end">

	<form id="ea-item-form" method="post" enctype="multipart/form-data">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php echo $title; ?></h3>
			</div>
			<div class="ea-card__inside">

				<div class="ea-row">
					<?php
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Name', 'wp-ever-accounting' ),
							'name'          => 'name',
							'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
							'tip'           => __( 'Enter Name', 'wp-ever-accounting' ),
							'value'         => $item->get_name(),
							'required'      => true,
						)
					);
					Form::category_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Category', 'wp-ever-accounting' ),
							'name'          => 'category_id',
							'value'         => $item->get_category_id(),
							'required'      => false,
							'type'          => 'item',
							'creatable'     => true,
							'ajax_action'   => 'ever_accounting_get_item_categories',
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
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Sale price', 'wp-ever-accounting' ),
							'name'          => 'sale_price',
							'placeholder'   => __( 'Enter Sale price', 'wp-ever-accounting' ),
							'value'         => $item->get_sale_price(),
							'required'      => true,
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Purchase price', 'wp-ever-accounting' ),
							'name'          => 'purchase_price',
							'placeholder'   => __( 'Enter Purchase price', 'wp-ever-accounting' ),
							'value'         => $item->get_purchase_price(),
							'required'      => true,
						)
					);
					if ( Tax::tax_enabled() ) :
						Form::text_input(
							array(
								'wrapper_class' => 'ea-col-6',
								'label'         => __( 'Sales Tax (%)', 'wp-ever-accounting' ),
								'name'          => 'sales_tax',
								'placeholder'   => __( 'Enter Sale price', 'wp-ever-accounting' ),
								'value'         => $item->get_sales_tax(),
								'type'          => 'number',
								'attr'          => array(
									'step' => .01,
									'min'  => 0,
									'max'  => 100,
								),
							)
						);

						Form::text_input(
							array(
								'wrapper_class' => 'ea-col-6',
								'label'         => __( 'Purchase Tax (%)', 'wp-ever-accounting' ),
								'name'          => 'purchase_tax',
								'placeholder'   => __( 'Enter Purchase price', 'wp-ever-accounting' ),
								'value'         => $item->get_purchase_tax(),
								'type'          => 'number',
								'attr'          => array(
									'step' => .01,
									'min'  => 0,
									'max'  => 100,
								),
							)
						);
					endif;
					Form::textarea(
						array(
							'label'         => __( 'Description', 'wp-ever-accounting' ),
							'name'          => 'description',
							'value'         => $item->get_description(),
							'required'      => false,
							'wrapper_class' => 'ea-col-6',
							'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
						)
					);

					Form::file_input(
						array(
							'label'         => __( 'Product Image', 'wp-ever-accounting' ),
							'name'          => 'thumbnail_id',
							'value'         => $item->get_thumbnail_id(),
							'required'      => false,
							'allowed-types' => 'jpg,jpeg,png',
							'wrapper_class' => 'ea-col-6',
							'placeholder'   => __( 'Upload Image', 'wp-ever-accounting' ),
						)
					);

					Form::hidden_input(
						array(
							'name'  => 'id',
							'value' => $item->get_id(),
						)
					);

					Form::hidden_input(
						array(
							'name'  => 'action',
							'value' => 'ever_accounting_edit_item',
						)
					);

					?>
				</div>


			</div>
			<div class="ea-card__footer">
				<?php
				wp_nonce_field( 'ea_edit_item' );
				submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
				?>
			</div>
		</div>
	</form>
<?php
$code     = Price::get_default_currency();
$currency = \Ever_Accounting\Currencies::get( $code );
ever_accounting_enqueue_js(
	"
	jQuery('#ea-item-form #purchase_price, #ea-item-form #sale_price').inputmask('decimal', {
			alias: 'numeric',
			groupSeparator: '" . $currency->get_thousand_separator() . "',
			autoGroup: true,
			digits: '" . $currency->get_precision() . "',
			radixPoint: '" . $currency->get_decimal_separator() . "',
			digitsOptional: false,
			allowMinus: false,
			prefix: '" . $currency->get_symbol() . "',
			placeholder: '0.000',
			rightAlign: 0,
			autoUnmask: true
		});
"
);
