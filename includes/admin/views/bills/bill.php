<?php
/**
 * Admin Bill Page.
 *
 * Page: Expenses
 * Tab: Bills
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Bills
 * @package     EverAccounting
 *
 * @var Bill $bill
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();
$company_details = array(
	'logo'       => eaccounting()->settings->get( 'company_logo', eaccounting()->plugin_url( '/assets/images/document-logo.png' ) ),
	'name'       => eaccounting()->settings->get( 'company_name' ),
	'street'     => eaccounting()->settings->get( 'company_address' ),
	'city'       => eaccounting()->settings->get( 'company_city' ),
	'state'      => eaccounting()->settings->get( 'company_state' ),
	'postcode'   => eaccounting()->settings->get( 'company_postcode' ),
	'country'    => eaccounting()->settings->get( 'company_country' ),
	'vat_number' => eaccounting()->settings->get( 'company_vat_number' ),
);
$countries       = eaccounting_get_countries();
$bill_actions    = apply_filters(
	'eaccounting_bill_actions',
	array(
		'status_received'  => __( 'Set Status to "Received"', 'wp-ever-accounting' ),
		'status_paid'      => __( 'Set Status to "paid"', 'wp-ever-accounting' ),
		'status_overdue'   => __( 'Set Status to "Overdue"', 'wp-ever-accounting' ),
		'status_cancelled' => __( 'Set Status to "Cancelled"', 'wp-ever-accounting' ),
	)
);

if ( $bill->exists() ) {
	//add_meta_box( 'bill_actions', __( 'Bill Actions', 'wp-ever-accounting' ), false, 'ea_bill', 'side' );
	add_meta_box( 'bill_notes', __( 'Bill Notes', 'wp-ever-accounting' ), array( 'EAccounting_Admin_Bills', 'bill_notes' ), 'ea_bill', 'side' );
	add_meta_box( 'bill_payments', __( 'Bill Payments', 'wp-ever-accounting' ), '__return_null', 'ea_bill', 'side' );
}
$del_url = wp_nonce_url( admin_url( 'admin.php?page=ea-expenses&tab=bills&action=delete&invoice_id=' . $bill->get_id() ), 'bill-nonce', '_wpnonce' );
/**
 * Fires after all built-in meta boxes have been added, contextually for the given object.
 *
 * @since 1.1.0
 *
 * @param Bill $bill object.
 */
do_action( 'add_meta_boxes_ea_bill', $bill );
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'BILL', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( 'admin.php?page=ea-expenses&tab=bills&action=add' ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>

	</div>

	<div>
		<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
			<button class="button-secondary ea-button-alert"><span><?php esc_html_e( 'Mark Bill Received', 'wp-ever-accounting' ); ?></span></button>
		<?php endif; ?>
	</div>
</div>
<hr class="wp-header-end">
<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
	<div class="notice error">
		<p><?php echo __( 'This is a <strong>DRAFT</strong> bill and will not be reflected until its marked as <strong>received</strong>.', 'wp-ever-accounting' ); ?></p>
	</div>
<?php endif; ?>

<div id="ea-bill" class="ea-clearfix">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">

				<div class="ea-card">
					<div class="ea-document__watermark">
						<p>
							<?php echo esc_html( $bill->get_status_nicename() ); ?>
						</p>
					</div>

					<div class="ea-document">
						<div class="ea-document__section border-bottom">
							<div class="ea-document__logo-wrap">
								<div class="ea-document__logo">
									<img src="<?php echo esc_url( $company_details['logo'] ); ?>" alt="<?php echo esc_html( $company_details['name'] ); ?>" height="100" width="100">
								</div>
							</div>

							<table class="ea-document__company_address">
								<tbody>
								<tr class="ea-document__company-name">
									<td><?php echo esc_html( $company_details['name'] ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html( $company_details['street'] ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html( implode( ' ', array_filter( array( $company_details['city'], $company_details['state'], $company_details['postcode'] ) ) ) ); ?></td>
								</tr>
								<tr>
									<td><?php echo isset( $countries[ $company_details['country'] ] ) ? esc_html( $countries[ $company_details['country'] ] ) : ''; ?></td>
								</tr>

								<tr>
									<td>
										<?php _e( 'VAT Number: ', 'wp-ever-accounting' ); ?>
										<?php echo empty( $company_details['vat_number'] ) ? '&mdash;' : esc_html( $company_details['vat_number'] ); ?>
									</td>
								</tr>

								</tbody>
							</table>
						</div>

						<div class="ea-document__section">
							<table class="ea-document__party-address">
								<tbody>
								<tr class="ea-document__company-name">
									<td><?php echo esc_html( $bill->get_name() ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html( $bill->get_street() ); ?></td>
								</tr>
								<tr>
									<td><?php echo esc_html( implode( ' ', array_filter( array( $bill->get_city(), $bill->get_state(), $bill->get_postcode() ) ) ) ); ?></td>
								</tr>
								<tr>
									<td><?php echo isset( $countries[ $bill->get_country() ] ) ? esc_html( $countries[ $bill->get_country() ] ) : ''; ?></td>
								</tr>

								</tbody>
							</table>

							<table class="ea-document__meta ">
								<tr>
									<td class="ea-document__meta-label"><?php _e( 'Bill Number', 'wp-ever-accounting' ); ?></td>
									<td class="ea-document__meta-content">
										<?php echo empty( $bill->get_bill_number() ) ? '&mdash;' : esc_html( $bill->get_bill_number( 'view' ) ); ?>
									</td>
								</tr>
								<tr>
									<td class="ea-document__meta-label"><?php _e( 'Order Number', 'wp-ever-accounting' ); ?></td>
									<td class="ea-document__meta-content">
										<?php echo empty( $bill->get_order_number() ) ? '&mdash;' : esc_html( $bill->get_order_number( 'view' ) ); ?>
									</td>
								</tr>
								<tr>
									<td class="ea-document__meta-label"><?php _e( 'Issue Date', 'wp-ever-accounting' ); ?></td>
									<td class="ea-document__meta-content">
										<?php echo empty( $bill->get_issue_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_issue_date(), 'M j, Y' ); ?>
									</td>
								</tr>
								<tr>
									<td class="ea-document__meta-label"><?php _e( 'Payment Date', 'wp-ever-accounting' ); ?></td>
									<td class="ea-document__meta-content">
										<?php echo empty( $bill->get_payment_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_payment_date(), 'M j, Y' ); ?>
									</td>
								</tr>
								<tr>
									<td class="ea-document__meta-label"><?php _e( 'Due Date', 'wp-ever-accounting' ); ?></td>
									<td class="ea-document__meta-content">
										<?php echo empty( $bill->get_due_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_due_date(), 'M j, Y' ); ?>
									</td>
								</tr>
							</table>
						</div>

						<?php
						eaccounting_get_admin_template(
							'bills/bill-items',
							array(
								'bill' => $bill,
								'mode' => 'view',
							)
						);
						?>
						<?php if ( ! empty( $bill->get_terms() ) ) : ?>
							<div class="ea-document__section">
								<div>
									<strong><?php _e( 'Terms:', 'wp-ever-accounting' ); ?></strong>
									<?php echo esc_html( $bill->get_terms() ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php eaccounting_do_meta_boxes( 'ea_bill', 'advanced', $bill ); ?>

			</div><!--/post-body-contentr-->
			<div id="postbox-container-1" class="postbox-container">
				<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
					<div class="ea-card">
						<div class="ea-card__header">
							<h3 class="ea-card__title">
								<?php esc_html_e( 'Bill Actions', 'wp-ever-accounting' ); ?>
							</h3>
						</div>
						<?php if ( $bill->needs_payment() ) :?>
						<div class="ea-card__section alt">
							<button class="button-secondary add-payment" type="button"><span><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></span></button>
							<button class="button-secondary"><span><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></span></button>
						</div>
						<?php endif; ?>
						<div class="ea-card__inside">
							<select name="bill_action" id="bill_action" style="width: 100%;" required>
								<option value=""><?php esc_html_e( 'Choose an action...', 'wp-ever-accounting' ); ?></option>
								<?php foreach ( $bill_actions as $action => $title ) { ?>
									<option value="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $title ); ?></option>
								<?php } ?>
								<input type="hidden" name="action" value="eaccounting_bill_action">
								<input type="hidden" name="bill_id" value="<?php echo esc_attr( $bill->get_id() ); ?>">
								<?php wp_nonce_field( 'ea_bill_action' ); ?>
							</select>
						</div>

						<div class="ea-card__footer">
							<a href="<?php echo esc_url( $del_url ); ?>"><?php esc_html_e( 'Remove', 'wp-ever-accounting' ); ?></a>
							<button class="button-primary"><span><?php esc_html_e( 'Apply', 'wp-ever-accounting' ); ?></span></button>
						</div>
					</div><!--.ea-card-->
				</form>

				<?php eaccounting_do_meta_boxes( 'ea_bill', 'side', $bill ); ?>
			</div><!--/postbox-container-->
			<div id="postbox-container-2" class="postbox-container">
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'normal', $bill ); ?>
			</div><!--/postbox-container-->
		</div><!--/post-body-->
	</div><!--/poststuff-->

</div><!--/ea-bill-->
<?php
if ( $bill->needs_payment() ) {
	eaccounting_get_admin_template( 'js/modal-bill-payment', array( 'bill' => $bill ) );
}
