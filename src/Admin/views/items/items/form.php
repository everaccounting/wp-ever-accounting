<?php
/**
 * Item form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $item \EverAccounting\Models\Item Item object.
 */

defined( 'ABSPATH' ) || exit;
?>

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
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
						<?php
						eac_input_field(
							array(
								'id'       => 'name',
								'name'     => 'name',
								'value'    => $item->name,
								'required' => true,
							)
						);
						?>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $item->name ); ?>" required/>
					</div>

					<div class="bkit-form-group">
						<label for="type">
							<?php esc_html_e( 'Type', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<select name="type" id="type" required>
							<option value=""><?php esc_html_e( 'Select type', 'wp-ever-accounting' ); ?></option>
							<?php foreach ( eac_get_item_types() as $type => $type_label ) : ?>
								<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $item->type, $type ); ?>><?php echo esc_html( $type_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="bkit-form-group">
						<label for="price">
							<?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<div class="bkit-input-group">
							<span class="addon"><?php echo esc_html( eac_get_base_currency() ); ?></span>
							<input type="text" name="price" id="price" class="eac_input_decimal" value="<?php echo esc_attr( $item->price ); ?>" placeholder="0.00" required/>
						</div>
					</div>

					<div class="bkit-form-group">
						<label for="cost">
							<?php esc_html_e( 'Cost', 'wp-ever-accounting' ); ?>
						</label>
						<div class="bkit-input-group">
							<span class="addon"><?php echo esc_html( eac_get_base_currency() ); ?></span>
							<input type="text" name="cost" id="cost" class="eac_input_decimal" value="<?php echo esc_attr( $item->cost ); ?>" placeholder="0.00"/>
						</div>
					</div>

					<div class="bkit-form-group">
						<label for="category_id">
							<?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?>
						</label>
						<div class="bkit-input-group">
							<select name="category_id" id="category_id" class="eac-select2" data-placeholder="<?php esc_attr_e( 'Select category', 'wp-ever-accounting' ); ?>" data-allow-clear="true">
								<option value=""><?php esc_html_e( 'Select category', 'wp-ever-accounting' ); ?></option>
								<?php
								foreach (
									eac_get_categories(
										array(
											'limit'  => - 1,
											'type'   => 'item',
											'status' => 'active',
										)
									) as $category
								) :
									?>
									<option value="<?php echo esc_attr( $category->id ); ?>" <?php selected( $item->category_id, $category->id ); ?>>
										<?php echo esc_html( $category->name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<a class="addon" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-misc&tab=categories&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add Category', 'wp-ever-accounting' ); ?>">
								<span class="dashicons dashicons-plus"></span>
							</a>
						</div>
					</div>

					<div class="bkit-form-group">
						<label for="unit">
							<?php esc_html_e( 'Unit', 'wp-ever-accounting' ); ?>
						</label>
						<select name="unit" id="unit" class="eac-select2">
							<option value=""><?php esc_html_e( 'Select unit', 'wp-ever-accounting' ); ?></option>
							<?php foreach ( eac_get_unit_types() as $unit => $unit_label ) : ?>
								<option value="<?php echo esc_attr( $unit ); ?>" <?php selected( $item->unit, $unit ); ?>><?php echo esc_html( $unit_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="bkit-form-group">
						<label for="taxable">
							<?php esc_html_e( 'Taxable', 'wp-ever-accounting' ); ?>
						</label>
						<select name="taxable" id="taxable">
							<option value="yes" <?php selected( $item->taxable, true ); ?>><?php esc_html_e( 'Yes', 'wp-ever-accounting' ); ?></option>
							<option value="no" <?php selected( $item->taxable, false ); ?>><?php esc_html_e( 'No', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>

					<div class="bkit-form-group">
						<label for="tax_ids">
							<?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?>
						</label>
						<select name="tax_ids[]" id="tax_ids" class="eac-select2" class="eac-select2" multiple>
							<?php
							foreach (
								eac_get_taxes(
									array(
										'limit'  => - 1,
										'status' => 'active',
									)
								) as $tax
							) :
								?>
								<option value="<?php echo esc_attr( $tax->id ); ?>" <?php selected( in_array( $tax->id, $item->tax_ids ), true ); ?>><?php echo esc_html( $tax->name ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'The selected tax rates will be applied to this item.', 'wp-ever-accounting' ); ?></p>
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

				<div class="bkit-card__body">
					<div class="bkit-form-group">
						<label for="status">
							<?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?>
						</label>
						<select name="status" id="status">
							<option value="active" <?php selected( 'active', $item->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
							<option value="inactive" <?php selected( 'inactive', $item->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
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
