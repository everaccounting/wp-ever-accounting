<?php
/**
 * Admin Item Edit Page.
 * Page: Items
 * Tab: Items
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $product_id
 */

defined( 'ABSPATH' ) || exit();

$product = new \EverAccounting\Models\Product( $product_id );
$title   = $product->exists() ? __( 'Update Product', 'wp-ever-accounting' ) : __( 'Add Product', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-products&tab=products' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $product->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-products&tab=products&action=delete&product_id=' . $product->get_id() ), 'bulk-products' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Product', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-item-form' ) ); ?>
		</div>
	</div>
<?php
require __DIR__ . '/product-form.php';
