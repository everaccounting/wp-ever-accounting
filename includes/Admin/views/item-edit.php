<?php
/**
 * Edit item view.
 *
 * @package EverAccounting
 * @var string $action Current action.
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

$id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$item = Item::make( $id );
?>
<h1 class="wp-heading-inline">
	<?php if ( $item->exists() ) : ?>
		<?php esc_html_e( 'Edit Item', 'wp-ever-accounting' ); ?>
	<?php else : ?>
		<?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?>
	<?php endif; ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-item-edit" name="item" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<?php
			/**
			 * Fires action to inject custom meta boxes in the main column.
			 *
			 * @param Item $item Customer object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_item_edit_core_meta_boxes', $item );
			?>
		</div><!-- .column-1 -->

		<div class="column-2">

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Item $item Customer object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_item_edit_side_meta_boxes', $item );
			?>

		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->
	<?php wp_nonce_field( 'eac_edit_item' ); ?>
	<input type="hidden" name="action" value="eac_edit_item"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
</form>
