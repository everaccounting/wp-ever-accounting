<?php
/**
 * Item form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $item Item Item object.
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;
?>

<form id="eac-item-edit" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<?php
				do_meta_boxes( 'eac_item_meta_boxes_core', 'side', $item );
				?>
			</div><!-- .eac-card -->
		</div><!-- .column-1 -->

		<div class="column-2">
			<?php
			/**
			 * Item form
			 *
			 * @since 1.0.0
			 * @package EverAccounting
			 * @var $item Item Item object.
			 */
			do_action( 'eac_edit_item_form', $item );
			?>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_item"/>
	<?php wp_nonce_field( 'eac_edit_item' ); ?>
</form>
