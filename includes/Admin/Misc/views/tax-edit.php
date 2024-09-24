<?php
/**
 * Admin Edit Tax View.
 * Page: Misc
 * Tab: Taxes
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $tax \EverAccounting\Models\Tax Tax object.
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Edit Tax', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( ['action', 'id' ]  ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
<?php
require __DIR__ . '/tax-form.php';
