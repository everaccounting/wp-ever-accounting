<?php
/**
 * View: Expenses export form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Export
 */

defined( 'ABSPATH' ) || exit();

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php echo esc_html__( 'Export expenses', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<div class="eac-col-6">
					<?php
					eac_input_field(
						array(
							'type'        => 'category',
							'subtype'     => 'expense',
							'name'        => 'category_id',
							'placeholder' => __( 'Export all categories', 'wp-ever-accounting' ),
							'label'       => __( 'Filter by category', 'wp-ever-accounting' ),
							'default'     => '',
						)
					)
					?>
				</div>
			</div>
		</div>
		<div class="eac-card__footer">
			<?php wp_nonce_field( 'eac_export_data' ); ?>
			<input name="action" value="eac_export_data" type="hidden">
			<input name="export_type" value="expenses" type="hidden">
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Export', 'wp-ever-accounting' ); ?></button>
		</div>
	</div>
</form>

