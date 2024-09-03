<?php
/**
 * Admin Edit Currency Form View.
 * Page: Misc
 * Tab: Currencies
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $currency \EverAccounting\Models\Currency Currency object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Edit Currency', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( 'view' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

<?php
require __DIR__ . '/form.php';
