<?php
/**
 * View invoice.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage EverAccounting/Admin/Views/Sales/Invoices
 *
 * @var EverAccounting\Models\Invoice $document
 */

defined( 'ABSPATH' ) || exit;

$payment = new EverAccounting\Models\Revenue();

$actions = array(
	array(
		'url'  => admin_url( 'admin.php?page=eac-sales&tab=invoices&action=edit&invoice_id=' . $document->id ),
		'text' => __( 'Edit', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=delete&invoice_id=' . $document->id ), 'eac_delete_invoice' ),
		'text' => __( 'Delete', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=clone&invoice_id=' . $document->id ), 'eac_clone_invoice' ),
		'text' => __( 'Clone', 'wp-ever-accounting' ),
	),
);
$actions = apply_filters( 'eac_invoice_actions', $actions, $document->id );
?>
<h1 class="wp-heading-inline">
	#<?php echo esc_html( $document->number ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'view' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<div class="bkit-row">
	<div class="bkit-col-9">
		<?php eac_get_template( 'invoice.php', array( 'invoice' => $document ) ); ?>

	</div>
	<div class="bkit-col-3">
	</div><!-- .bkit-col-3 -->
</div>
