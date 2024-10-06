<?php
/**
 * Admin Transfers Form.
 * Page: Expenses
 * Tab: Transfers
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $transfer \EverAccounting\Models\Transfer Transfer object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-transfer-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Transfer Attributes', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="eac-card__body grid--fields">
					<?php
					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'from_account_id',
							'label'       => __( 'From Account', 'wp-ever-accounting' ),
							'options'          => array( $transfer->from_account ),
							'value'            => $transfer->from_account_id,
							'class'            => 'eac_select2',
							'tooltip'          => __( 'Select the account.', 'wp-ever-accounting' ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'account',
							'required'         => true,
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
								__( 'Add Account', 'wp-ever-accounting' )
							)
						)
					);
					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'to_account_id',
							'label'       => __( 'To Account', 'wp-ever-accounting' ),
							'options'          => array( $transfer->to_account ),
							'value'            => $transfer->to_account_id,
							'class'            => 'eac_select2',
							'tooltip'          => __( 'Select the account.', 'wp-ever-accounting' ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'account',
							'required'         => true,
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
								__( 'Add Account', 'wp-ever-accounting' )
							),
						)
					);
					eac_form_field(
						array(
							'type'        => 'text',
							'name'        => 'amount',
							'label'       => __( 'Amount', 'wp-ever-accounting' ),
							'placeholder' => '0.00',
							'value'       => $transfer->amount,
							'required'    => true,
							'data-currency' => $transfer->currency,
							'class'         => 'eac_amount',
						)
					);
					eac_form_field(
						array(
							'data_type'   => 'date',
							'name'        => 'date',
							'label'       => __( 'Date', 'wp-ever-accounting' ),
							'placeholder' => 'YYYY-MM-DD',
							'value'       => $transfer->date,
							'required'    => true,
							'class'       => 'eac_datepicker',
						)
					);

					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'method',
							'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
							'value'       => $transfer->method,
							'options'     => eac_get_payment_methods(),
							'placeholder' => __( 'Select payment method', 'wp-ever-accounting' ),
						)
					);
					eac_form_field(
						array(
							'type'        => 'text',
							'name'        => 'reference',
							'label'       => __( 'Reference', 'wp-ever-accounting' ),
							'value'       => $transfer->reference,
							'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						)
					);
					eac_form_field(
						array(
							'type'        => 'textarea',
							'name'        => 'note',
							'label'       => __( 'Notes', 'wp-ever-accounting' ),
							'value'       => $transfer->note,
							'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
							'wrapper_class' => 'is--full',
						)
					);
					?>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
				</div><!-- .eac-card__header -->
				<div class="eac-card__footer">
					<?php if ( $transfer->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-purchases&tab=expenses&id=' . $transfer->id ) ), 'bulk-transfer' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary tw-h-full"><?php esc_html_e( 'Update Transfer', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Transfer', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div><!-- .eac-card__footer -->
			</div><!-- .eac-card -->
		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_transfer' ); ?>
	<input type="hidden" name="action" value="eac_edit_transfer"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $transfer->id ); ?>"/>
</form>
<?php
