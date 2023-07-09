<?php
/**
 * Send invoice view
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var Invoice $document Invoice object.
 */

use \EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

?>
<form id="eac-send-invoice-form" class="eac-ajax-form" method="post" autocomplete="off">
	<?php
	eac_form_field( array(
		'type'     => 'email',
		'name'     => 'from',
		'label'    => __( 'From', 'wp-ever-accounting' ),
		'value'    => get_option( 'admin_email' ),
		'required' => true,
	) );

	eac_form_field( array(
		'type'     => 'email',
		'name'     => 'to',
		'label'    => __( 'To', 'wp-ever-accounting' ),
		'value'    => $document->get_billing_email(),
		'required' => true,
	) );

	eac_form_field( array(
		'type'     => 'text',
		'name'     => 'subject',
		'label'    => __( 'Subject', 'wp-ever-accounting' ),
		'value'    => sprintf( __( 'Invoice #%s', 'wp-ever-accounting' ), $document->get_number() ),
		'required' => true,
	) );

	eac_form_field( array(
		'id'       => 'eac-send-invoice-message',
		'type'     => 'wp_editor',
		'name'     => 'message',
		'label'    => __( 'Message', 'wp-ever-accounting' ),
		'value'    => __( 'You have received an invoice from {company_name}. To view your invoice, click the link below: \n\n <a href="{invoice_link}">View Invoice</a>', 'wp-ever-accounting' ),
		'required' => true,
	) );
	?>
	<p>
		<?php esc_html_e( 'Available tags:', 'wp-ever-accounting' ); ?>
	</p>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e('Tag', 'wp-ever-accounting'); ?></th>
				<th><?php esc_html_e('Description', 'wp-ever-accounting'); ?></th>
			</tr>
		</thead>
		<tbody>
		<!--name-->
			<tr>
				<td>{billing_name}</td>
				<td><?php esc_html_e('Customer name', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{billing_company}</td>
				<td><?php esc_html_e('Customer company', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{billing_address}</td>
				<td><?php esc_html_e('Customer address', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{billing_phone}</td>
				<td><?php esc_html_e('Customer phone', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{billing_email}</td>
				<td><?php esc_html_e('Customer email', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{invoice_total}</td>
				<td><?php esc_html_e('Invoice total', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{invoice_number}</td>
				<td><?php esc_html_e('Invoice number', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{issue_date}</td>
				<td><?php esc_html_e('Invoice issue date', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{due_date}</td>
				<td><?php esc_html_e('Invoice due date', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{total_paid}</td>
				<td><?php esc_html_e('Invoice total paid', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{balance}</td>
				<td><?php esc_html_e('Invoice balance', 'wp-ever-accounting'); ?></td>
			</tr>
			<tr>
				<td>{invoice_link}</td>
				<td><?php esc_html_e('Invoice link', 'wp-ever-accounting'); ?></td>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript">
	// When document is ready eac-send-invoice-form will be initialized with wp editor.
	jQuery( document ).ready( function ( $ ) {
		wp.editor.initialize ( 'eac-send-invoice-message', {
			tinymce: {
				wpautop: false
			},
			quicktags: true
		} );
	} );
</script>
