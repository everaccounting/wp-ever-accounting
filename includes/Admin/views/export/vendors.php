<?php
/**
 * View: Vendors export form
 *
 * @since 1.1.6
 * @package EverAccounting
 * @subpackage Admin/View/Export
 */

defined( 'ABSPATH' ) || exit();

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<div class="bkit-card">
		<div class="bkit-card__header">
			<h2 class="bkit-card__title"><?php echo esc_html__( 'Export Vendors', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="bkit-card__body">
			<div class="bkit-columns">
				<div class="bkit-col-6">
					<?php
					eac_form_group(
						array(
							'label'   => __( 'Filter by status', 'wp-ever-accounting' ),
							'type'    => 'select',
							'name'    => 'status',
							'default' => '',
							'options' => array(
								''         => __( 'All', 'wp-ever-accounting' ), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Already escaped.
								'active'   => __( 'Active', 'wp-ever-accounting' ),
								'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
							),
						)
					);
					// TODO: Need to fixed the country select field.
					eac_form_group(
						array(
							'type'        => 'country',
							'name'        => 'country',
							'placeholder' => __( 'Export all countries', 'wp-ever-accounting' ),
							'label'       => __( 'Filter by country', 'wp-ever-accounting' ),
							'default'     => '',
						)
					)
					?>
				</div>
			</div>
		</div>
		<div class="bkit-card__footer">
			<?php wp_nonce_field( 'eac_export_data' ); ?>
			<input name="action" value="eac_export_data" type="hidden">
			<input name="export_type" value="vendors" type="hidden">
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Export', 'wp-ever-accounting' ); ?></button>
		</div>
	</div>
</form>

