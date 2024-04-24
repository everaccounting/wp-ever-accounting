<?php
/**
 * Currency view.
 *
 * @package EverAccounting\Admin\Views\Categories
 * @version 1.0.0
 * @var $category \EverAccounting\Models\Category Category object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Category Details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">

					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $category->name ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="type">
							<?php esc_html_e( 'Type', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<select name="type" id="type">
							<option value=""><?php esc_html_e( 'Select type', 'wp-ever-accounting' ); ?></option>
							<?php foreach ( eac_get_category_types() as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $category->type ); ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="bkit-form-group is--full">
						<label for="description"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
						<textarea name="description" id="description"><?php echo esc_html( $category->description ); ?></textarea>
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
							<option value="active" <?php selected( 'active', $category->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
							<option value="inactive" <?php selected( 'inactive', $category->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
				</div>

				<div class="bkit-card__footer">
					<?php if ( $category->exists() ) : ?>
						<input type="hidden" name="id" value="<?php echo esc_attr( $category->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_category"/>
					<?php wp_nonce_field( 'eac_edit_category' ); ?>
					<?php if ( $category->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-categories&id=' . $category->id ) ), 'bulk-categories' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
					<?php if ( $category->exists() ) : ?>
						<button class="button button-primary"><?php esc_html_e( 'Update', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
