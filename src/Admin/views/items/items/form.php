<?php
/**
 * Item form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $item \EverAccounting\Models\Item Item object.
 */

defined( 'ABSPATH' ) || exit;
$state = wp_interactivity_state( 'eac/item', $item->to_array() );
?>

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" data-wp-interactive="eac/item">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">

					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $item->name ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="category_id">
							<?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<div class="bkit-input-group">
							<select name="category_id" id="category_id">
								<option value=""><?php esc_html_e( 'Select category', 'wp-ever-accounting' ); ?><?php foreach ( $item->categories() as $category ) : ?>
								<option value="<?php esc_attr( $category->id ); ?>"><?php echo esc_html( $category->name ); ?></option>
								<?php endforeach; ?>
							</select>
							<a class="addon" href="#">Add</a>
						</div>
					</div>

					<div class="bkit-form-group">
						<label for="sale_price"><?php esc_html_e( 'Sale Price', 'wp-ever-accounting' ); ?></label>
						<div class="bkit-input-group">
							<span class="addon"><?php echo esc_html( eac_get_base_currency() ); ?></span>
							<input type="text" name="sale_price" id="sale_price" class="eac_input_decimal" value="<?php echo esc_attr( $item->sale_price ); ?>" placeholder="0.00"/>
						</div>
					</div>

					<div class="bkit-form-group">
						<label for="purchase_price"><?php esc_html_e( 'Purchase Price', 'wp-ever-accounting' ); ?></label>
						<div class="bkit-input-group">
							<span class="addon"><?php echo esc_html( eac_get_base_currency() ); ?></span>
							<input type="text" name="purchase_price" id="purchase_price" class="eac_input_decimal" value="<?php echo esc_attr( $item->purchase_price ); ?>" placeholder="0.00"/>
						</div>
					</div>

					<div class="bkit-form-group is--full">
						<label for="description"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
						<textarea type="text" name="description" id="description"><?php echo esc_html( $item->description ); ?></textarea>
					</div>

				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__footer">
					<?php if ( $item->exists() ) : ?>
						<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_item"/>
					<?php wp_nonce_field( 'eac_edit_item' ); ?>
					<?php if ( $item->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-items&id=' . $item->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
					<?php if ( $item->exists() ) : ?>
						<button class="button button-primary"><?php esc_html_e( 'Update Item', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
