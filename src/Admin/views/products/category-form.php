<?php
/**
 * View: Category Form.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Term $term Item object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-category-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Category Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'       => 'name',
						'label'    => __( 'Name', 'wp-ever-accounting' ),
						'value'    => $term->get_name(),
						'class'    => 'eac-col-12',
						'required' => true,
					)
				);
				eac_input_field(
					array(
						'id'    => 'description',
						'label' => __( 'Description', 'wp-ever-accounting' ),
						'value' => $term->get_description(),
						'class' => 'eac-col-12',
						'type'  => 'textarea',
					)
				);
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_category' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $term->get_id() ); ?>">
	<input type="hidden" name="group" value="product_cat">
	<input type="hidden" name="action" value="eac_edit_category">
</form>

