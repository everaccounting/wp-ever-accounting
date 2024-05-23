<?php
/**
 * Admin Invoices Form.
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns            = $document::get_line_columns();
$columns['actions'] = '&nbsp;';

if ( ( ! eac_tax_enabled() || $document->vat_exempt ) && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}

?>
<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">
	<div class="bkit-row">
		<div class="eac-document-form__body bkit-col-9">
			<?php require __DIR__ . '/form-body.php'; ?>
		</div><!-- .bkit-col-9 -->
		<div class="eac-document-form__sidebar bkit-col-3">
			<?php require __DIR__ . '/form-sidebar.php'; ?>
		</div><!-- .eac-document-form__aside -->
	</div><!-- .bkit-row -->

	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $document->id ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
<?php
