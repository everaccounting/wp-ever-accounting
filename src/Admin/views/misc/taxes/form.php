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
<form id="eac-tax-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Tax rate details', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__body grid--fields">
					<?php
					eac_form_group(
						array(
							'id'          => 'name',
							'label'       => __( 'Name', 'wp-ever-accounting' ),
							'placeholder' => __( 'Enter tax rate name', 'wp-ever-accounting' ),
							'value'       => $tax->name,
							'required'    => true,
						)
					);
					eac_form_group(
						array(
							'data_type'   => 'decimal',
							'id'          => 'rate',
							'label'       => __( 'Rate (%)', 'wp-ever-accounting' ),
							'placeholder' => __( 'Enter tax rate', 'wp-ever-accounting' ),
							'value'       => $tax->rate,
							'required'    => true,
						)
					);
					eac_form_group(
						array(
							'id'          => 'is_compound',
							'label'       => __( 'Is compound', 'wp-ever-accounting' ),
							'placeholder' => __( 'Select if tax is compound', 'wp-ever-accounting' ),
							'value'       => $tax->is_compound,
							'required'    => true,
							'options'     => array(
								'yes' => __( 'Yes', 'wp-ever-accounting' ),
								'no'  => __( 'No', 'wp-ever-accounting' ),
							),
							'type'        => 'select',
						)
					);
					eac_form_group(
						array(
							'id'            => 'description',
							'label'         => __( 'Description', 'wp-ever-accounting' ),
							'placeholder'   => __( 'Enter tax rate description', 'wp-ever-accounting' ),
							'value'         => $tax->description,
							'wrapper_class' => 'is--full',
							'type'          => 'textarea',
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
							'value'       => $tax->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
						)
					);
					?>
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
