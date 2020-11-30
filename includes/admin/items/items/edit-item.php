<?php
/**
 * Admin Item Edit Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Items/Categories
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$item_id = isset( $_REQUEST['item_id'] ) ? absint( $_REQUEST['item_id'] ) : null;
try {
	$item = new \EverAccounting\Models\Item( $item_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'item_id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $item->exists() ? __( 'Update Item', 'wp-ever-accounting' ) : __( 'Add Item', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-category-form" class="ea-ajax-form" method="post">
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

				?>
			</div>
		</form>
	</div>
</div>
