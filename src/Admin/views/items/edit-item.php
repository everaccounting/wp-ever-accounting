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
$item  = new \EverAccounting\Models\Item( $item_id );
$title = $item->exists() ? __( 'Update Item', 'wp-ever-accounting' ) : __( 'Add Item', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&tab=items' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $item->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-items&tab=items&action=delete&item_id=' . $item->get_id() ), 'bulk-items' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Item', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-item-form' ) ); ?>
		</div>
	</div>

<div class="eac-poststuff">
	<div class="eac-poststuff__body">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3><?php esc_html_e( 'Item Details', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias, corporis!
			</div>
		</div>
	</div>
	<aside class="eac-poststuff__sidebar">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3><?php esc_html_e( 'Item Details', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias, corporis!
			</div>
		</div>
	</aside>
</div>
<?php
require __DIR__ . '/item-form.php';
