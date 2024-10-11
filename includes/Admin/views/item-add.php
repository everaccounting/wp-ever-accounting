<?php
/**
 * Add item view.
 *
 * @package EverAccounting
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

$item = new Item();

?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'action' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-item-edit" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">

		<div class="column-1">

			<?php
			/**
			 * Fires hook in the main area of the item add form to add custom fields.
			 *
			 * @param $item Item Item object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_item_add_primary', $item );
			?>

		</div><!-- .column-1 -->

		<div class="column-2">

			<?php
			/**
			 * Fires hook in the sidebar area of the item add form to add custom fields.
			 *
			 * @param $item Item Item object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_item_add_secondary', $item );
			?>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_item' ); ?>
	<input type="hidden" name="action" value="eac_edit_item"/>
	<input type="hidden" name="referredby" value="<?php echo esc_attr( wp_get_referer() ); ?>"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
</form>


