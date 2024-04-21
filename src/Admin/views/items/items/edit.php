<?php
/**
 * Add item view.
 *
 * @package EverAccounting
 * @var $item \EverAccounting\Models\Item
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Edit Item', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'edit' ) ); ?>" class="button">
		<?php esc_html_e( 'Go back', 'wp-ever-accounting' ); ?>
	</a>
</h1>
<?php
require __DIR__ . '/form.php';
