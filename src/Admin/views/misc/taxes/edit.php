<?php
/**
 * Edit tax view.
 *
 * @package EverAccounting
 * @var $tax \EverAccounting\Models\Tax
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Edit Tax', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'edit' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
<?php
require __DIR__ . '/form.php';
