<?php
/**
 * Admin Revenue Form.
 * Page: Sales
 * Tab: Revenue
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $revenue \EverAccounting\Models\Revenue Revenue object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<form id="eac-revenue-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<div class="bkit-poststuff">
			<div class="column-1">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Revenue details', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="bkit-card__body grid--fields">
						<?php
						eac_form_group(
							array(
								'label'                      => __( 'Date', 'wp-ever-accounting' ),
								'type'                       => 'date',
								'name'                       => 'date',
								'placeholder'                => 'YYYY-MM-DD',
								'value'                      => $revenue->date,
								'required'                   => true,
								'class'                      => 'eac_inputmask',
								'data-inputmask-alias'       => 'datetime',
								'data-inputmask-inputformat' => 'yyyy-mm-dd',
							)
						);

						eac_form_group(
							array(
								'label'            => __( 'Account', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'account_id',
								'options'          => array( $revenue->account ),
								'value'            => $revenue->account_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'tooltip'          => __( 'Select the receiving account.', 'wp-ever-accounting' ),
								'disabled'         => $revenue->exists(),
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'account',
								'suffix'           => sprintf(
									'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
									__( 'Add Account', 'wp-ever-accounting' )
								),
							)
						);
						eac_form_group(
							array(
								'label'          => __( 'Amount', 'wp-ever-accounting' ),
								'name'           => 'amount',
								'placeholder'    => '0.00',
								'value'          => $revenue->amount,
								'required'       => true,
								'class'          => 'eac_inputmask',
								'tooltip'        => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
								'data-inputmask' => 'currency',
							)
						);
						eac_form_group(
							array(
								'label'            => __( 'Customer', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'value'            => $revenue->contact_id,
								'options'          => array( $revenue->customer ),
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'default'          => filter_input( INPUT_GET, 'customer_id', FILTER_SANITIZE_NUMBER_INT ),
								'data-placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'customer',
								'class'            => 'eac_select2',
								'suffix'           => sprintf(
									'<a class="button" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ),
									__( 'Add customer', 'wp-ever-accounting' )
								),
							)
						);
						eac_form_group(
							array(
								'label'            => __( 'Category', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'category_id',
								'value'            => $revenue->category_id,
								'options'          => array( $revenue->category ),
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'placeholder'      => __( 'Select category', 'wp-ever-accounting' ),
								'class'            => 'eac_select2',
								'data-placeholder' => __( 'Select category', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'category',
								'data-subtype'     => 'income',
								'suffix'           => sprintf(
									'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-misc&tab=categories&add=yes&type=income' ) ),
									__( 'Add Category', 'wp-ever-accounting' )
								),
							)
						);
						eac_form_group(
							array(
								'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
								'type'        => 'select',
								'name'        => 'payment_method',
								'value'       => $revenue->payment_method,
								'options'     => eac_get_payment_methods(),
								'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
							)
						);
						$invoices = array();
						eac_form_group(
							array(
								'label'            => __( 'Invoice', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'document_id',
								'value'            => $revenue->document_id,
								'default'          => filter_input( INPUT_GET, 'document_id', FILTER_SANITIZE_NUMBER_INT ),
								'options'          => wp_list_pluck( $revenue->invoice, 'formatted_name', 'id' ),
								'placeholder'      => __( 'Select invoice', 'wp-ever-accounting' ),
								'required'         => false,
								'class'            => 'eac_select2',
								'data-placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
								'tooltip'          => __( 'Select the invoice related to this revenue., ignore if not applicable.', 'wp-ever-accounting' ),
							)
						);
						eac_form_group(
							array(
								'label'       => __( 'Reference', 'wp-ever-accounting' ),
								'type'        => 'text',
								'name'        => 'reference',
								'value'       => $revenue->reference,
								'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
							)
						);
						eac_form_group(
							array(
								'label'         => __( 'Notes', 'wp-ever-accounting' ),
								'type'          => 'textarea',
								'name'          => 'note',
								'value'         => $revenue->note,
								'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
								'wrapper_class' => 'is--full',
							)
						);
						?>
					</div>
				</div>
			</div><!-- .column-1 -->

			<div class="column-2">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="bkit-card__body">
						<?php
						eac_form_group(
							array(
								'label'       => __( 'Status', 'wp-ever-accounting' ),
								'type'        => 'select',
								'id'          => 'status',
								'options'     => eac_get_transaction_statuses(),
								'value'       => $revenue->status,
								'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							)
						);
						?>
					</div>
					<div class="bkit-card__footer">
						<?php if ( $revenue->exists() ) : ?>
							<input type="hidden" name="account_id" value="<?php echo esc_attr( $revenue->account_id ); ?>"/>
							<input type="hidden" name="id" value="<?php echo esc_attr( $revenue->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_revenue"/>
						<?php wp_nonce_field( 'eac_edit_revenue' ); ?>
						<?php if ( $revenue->exists() ) : ?>
							<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=revenues&id=' . $revenue->id ) ), 'bulk-revenues' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $revenue->exists() ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Update Revenue', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
							<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Revenue', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>


			</div><!-- .column-2 -->

		</div><!-- .bkit-poststuff -->
	</form>
<?php
