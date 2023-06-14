<?php
/**
 * View: Tax rate Form.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Tax $tax Tax rate object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-tax-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Tax Rate Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'name',
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'placeholder' => __( 'Enter tax rate name', 'wp-ever-accounting' ),
						'value'       => $tax->get_name(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'type'        => 'decimal',
						'id'          => 'rate',
						'label'       => __( 'Rate (%)', 'wp-ever-accounting' ),
						'placeholder' => __( 'Enter tax rate', 'wp-ever-accounting' ),
						'value'       => $tax->get_rate(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'id'          => 'is_compound',
						'label'       => __( 'Is compound', 'wp-ever-accounting' ),
						'placeholder' => __( 'Select if tax is compound', 'wp-ever-accounting' ),
						'value'       => $tax->get_is_compound(),
						'class'       => 'eac-col-6',
						'required'    => true,
						'options'     => array(
							'yes' => __( 'Yes', 'wp-ever-accounting' ),
							'no'  => __( 'No', 'wp-ever-accounting' ),
						),
						'type'        => 'select',
					)
				);
				eac_form_field(
					array(
						'id'          => 'description',
						'label'       => __( 'Description', 'wp-ever-accounting' ),
						'placeholder' => __( 'Enter tax rate description', 'wp-ever-accounting' ),
						'value'       => $tax->get_description(),
						'class'       => 'eac-col-12',
						'type'        => 'textarea',
					)
				);
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_tax' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $tax->get_id() ); ?>">
	<input type="hidden" name="action" value="eac_edit_tax">
</form>

