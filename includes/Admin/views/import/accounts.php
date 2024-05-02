<?php
/**
 * View: Accounts import.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Admin/View/Import
 */

defined( 'ABSPATH' ) || exit();

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<div class="bkit-card">
		<div class="bkit-card__header">
			<h2 class="bkit-card__title"><?php echo esc_html__( 'Import Accounts', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="bkit-card__body">
			<div class="bkit-columns">
				<div class="bkit-col-6">
					<p>
						<?php
						/* translators: %s: CSV file */
						echo wp_kses_post( sprintf( __( 'Import accounts from CSV file. Download a <a href="%s"> sample </a> file to learn how to format the CSV file.', 'wp-ever-accounting' ), EAC()->get_dir_url( '/sample-data/import/accounts.csv' ) ) );
						?>
					</p>
					<input name="upload" type="file" required="required" accept="text/csv">
				</div>
			</div>
		</div>
		<div class="bkit-card__footer">
			<?php wp_nonce_field( 'eac_import_data' ); ?>
			<input name="action" value="eac_import_data" type="hidden">
			<input name="import_type" value="accounts" type="hidden">
			<button type="submit" class="button button-primary"><?php echo esc_html__( 'Import CSV', 'wp-ever-accounting' ); ?></button>
		</div>
	</div>
</form>
