<?php
/**
 * Admin Category Form.
 * Page: Misc
 * Tab: Categories
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $category \EverAccounting\Models\Category Category object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-category-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Category Details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">
					<?php
					eac_form_group(
						array(
							'id'          => 'name',
							'label'       => __( 'Name', 'wp-ever-accounting' ),
							'placeholder' => __( 'Enter category name', 'wp-ever-accounting' ),
							'value'       => $category->name,
							'required'    => true,
						)
					);
					eac_form_group(
						array(
							'id'          => 'type',
							'type'        => 'select',
							'label'       => __( 'Type', 'wp-ever-accounting' ),
							'placeholder' => __( 'Select category type', 'wp-ever-accounting' ),
							'value'       => $category->type,
							'default'     => isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
							'options'     => eac_get_category_types(),
						)
					);
					eac_form_group(
						array(
							'id'            => 'description',
							'label'         => __( 'Description', 'wp-ever-accounting' ),
							'placeholder'   => __( 'Enter category description', 'wp-ever-accounting' ),
							'value'         => $category->description,
							'type'          => 'textarea',
							'wrapper_class' => 'is--full',
						)
					);
					?>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body">
					<?php
					eac_form_group(
						array(
							'type'        => 'select',
							'id'          => 'status',
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'options'     => array(
								'active'   => __( 'Active', 'wp-ever-accounting' ),
								'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
							),
							'value'       => $category->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
						)
					);
					?>
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
						<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Update', 'wp-ever-accounting' ); ?>"/>
					<?php else : ?>
						<input type="submit" class="button button-primary bkit-w-100" value="<?php esc_attr_e( 'Add', 'wp-ever-accounting' ); ?>"/>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
