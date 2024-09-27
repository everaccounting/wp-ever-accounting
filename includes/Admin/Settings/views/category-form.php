<?php
/**
 * Admin Category Form.
 * Page: Misc
 * Tab: Categories
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $category Category Category object.
 */

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-category-form" method="post">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Category Attributes', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="eac-card__body grid--fields">
					<?php
					eac_form_field(
						array(
							'id'          => 'name',
							'label'       => __( 'Name', 'wp-ever-accounting' ),
							'placeholder' => __( 'Enter category name', 'wp-ever-accounting' ),
							'value'       => $category->name,
							'required'    => true,
						)
					);
					eac_form_field(
						array(
							'id'          => 'type',
							'type'        => 'select',
							'label'       => __( 'Type', 'wp-ever-accounting' ),
							'placeholder' => __( 'Select category type', 'wp-ever-accounting' ),
							'value'       => $category->type,
							'default'     => isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
							'options'     => EAC()->categories->get_types(),
							'required'    => true,
						)
					);
					eac_form_field(
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
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="eac-card__footer">
					<?php if ( $category->exists() ) : ?>
						<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Update Category', 'wp-ever-accounting' ); ?>"/>
					<?php else : ?>
						<input type="submit" class="button button-primary tw-w-full" value="<?php esc_attr_e( 'Add Category', 'wp-ever-accounting' ); ?>"/>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_category' ); ?>
	<input type="hidden" name="action" value="eac_edit_category"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $category->id ); ?>"/>
</form>
