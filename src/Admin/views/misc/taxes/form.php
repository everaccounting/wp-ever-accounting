<?php
/**
 * Tax form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $tax \EverAccounting\Models\Tax Tax object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-taxes-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Tax rate details', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__body grid--fields">

					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $tax->name ); ?>" required/>
					</div>

					<div class="bkit-form-group">
						<label for="rate">
							<?php esc_html_e( 'Rate', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="rate" id="rate" value="<?php echo esc_attr( $tax->rate ); ?>" required/>
					</div>

					<div class="bkit-form-group">
						<label for="is_compound">
							<?php esc_html_e( 'Is compound', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<select name="is_compound" id="is_compound" required="required">
							<option value=""><?php esc_html_e( 'Select if tax is compound', 'wp-ever-accounting' ); ?></option>
							<option value="yes" <?php selected( true, $tax->is_compound ); ?>><?php esc_html_e( 'Yes', 'wp-ever-accounting' ); ?></option>
							<option value="no" <?php selected( false, $tax->is_compound ); ?>><?php esc_html_e( 'No', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>

					<div class="bkit-form-group is--full">
						<label for="description"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
						<textarea type="text" name="description" id="description"><?php echo esc_html( $tax->description ); ?></textarea>
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
							<option value="active" <?php selected( 'active', $tax->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
							<option value="inactive" <?php selected( 'inactive', $tax->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
				</div>

				<div class="bkit-card__footer">
					<?php if ( $tax->exists() ) : ?>
						<input type="hidden" name="id" value="<?php echo esc_attr( $tax->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_tax"/>
					<?php wp_nonce_field( 'eac_edit_tax' ); ?>
					<?php if ( $tax->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-misc&tab=taxes&id=' . $tax->id ) ), 'bulk-taxes' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
					<?php if ( $tax->exists() ) : ?>
						<button class="button button-primary"><?php esc_html_e( 'Update Tax', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
