<?php
/**
 * Edit invoice view.
 *
 * @package EverAccounting
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
<?php
require __DIR__ . '/invoices-form.php';
