<?php
/**
 * Invoice Line Items
 *
 * @package eaccounting\Admin\Views
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$items = $invoice->get_line_items();
?>

<?php foreach ( $items as $index => $item ) : ?>
	<?php eaccounting_get_admin_template( 'invoice/line-item', array( 'invoice' => $invoice, 'item' => $item, 'index' => $index ) ); ?>
<?php endforeach; ?>
