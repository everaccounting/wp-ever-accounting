<?php
/**
 * Add payment view.
 *
 * @package EverAccounting
 * @var &payment \EverAccounting\Models\Payment
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" class="page-title-action" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

<?php
require __DIR__ . '/form.php';
