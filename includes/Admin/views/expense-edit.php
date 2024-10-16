<?php
/**
 * Expense Edit view.
 *
 * This page handles the views of the expense edit page.
 *
 * @since 1.0.0
 *
 * @subpackage EverAccounting/Admin/Views
 * @package EverAccounting
 * @var Expense $expense Expense object.
 */

use EverAccounting\Models\Expense;

defined( 'ABSPATH' ) || exit;

$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$expense = Expense::make( $id );

?>
<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php if ( $expense->exists() ) : ?>
			<?php esc_html_e( 'Edit Expense', 'wp-ever-accounting' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Add Expense', 'wp-ever-accounting' ); ?>
		<?php endif; ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

	<?php if ( $expense->exists() ) : ?>
		<a class="button" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view' ) ) ); ?>">
			<?php esc_html_e( 'View Expense', 'wp-ever-accounting' ); ?>
		</a>
	<?php endif; ?>
</div>

<form id="eac-edit-expense" name="expense" method="post">

	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Exxpense Attributes', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body grid--fields">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Date', 'wp-ever-accounting' ),
							'type'        => 'date',
							'name'        => 'date',
							'placeholder' => 'yyyy-mm-dd',
							'value'       => $expense->date,
							'required'    => true,
							'class'       => 'eac_datepicker',
						)
					);

					eac_form_field(
						array(
							'label'       => __( 'Expense #', 'wp-ever-accounting' ),
							'type'        => 'text',
							'name'        => 'expense_number',
							'value'       => $expense->number,
							'default'     => $expense->get_next_number(),
							'placeholder' => $expense->get_next_number(),
							'required'    => true,
							'readonly'    => true,
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Account', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'account_id',
							'options'          => array( $expense->account ),
							'value'            => $expense->account_id,
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

					// exchange rate.
					eac_form_field(
						array(
							'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'type'        => 'number',
							'name'        => 'exchange_rate',
							'value'       => $expense->exchange_rate,
							'placeholder' => '1.00',
							'required'    => true,
							'class'       => 'eac_exchange_rate',
							'prefix'      => '1 ' . eac_base_currency() . ' = ',
							'attr-step'   => 'any',
						)
					);

					eac_form_field(
						array(
							'label'         => __( 'Amount', 'wp-ever-accounting' ),
							'name'          => 'amount',
							'placeholder'   => '0.00',
							'value'         => $expense->amount,
							'required'      => true,
							'tooltip'       => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
							'data-currency' => $expense->currency,
							'class'         => 'eac_amount',
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Category', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'category_id',
							'value'            => $expense->category_id,
							'options'          => array( $expense->category ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'placeholder'      => __( 'Select category', 'wp-ever-accounting' ),
							'class'            => 'eac_select2',
							'data-placeholder' => __( 'Select category', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'category',
							'data-subtype'     => 'expense',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-misc&tab=categories&add=yes&type=income' ) ),
								__( 'Add Category', 'wp-ever-accounting' )
							),
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Vendor', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'contact_id',
							'options'          => array( $expense->vendor ),
							'value'            => $expense->vendor_id,
							'class'            => 'eac_select2',
							'tooltip'          => __( 'Select the vendor.', 'wp-ever-accounting' ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'data-placeholder' => __( 'Select a vendor', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'vendor',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-purchases&tab=vendors&action=add' ) ),
								__( 'Add Vendor', 'wp-ever-accounting' )
							),
						)
					);

					eac_form_field(
						array(
							'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
							'type'        => 'select',
							'name'        => 'payment_method',
							'value'       => $expense->payment_method,
							'options'     => eac_get_payment_methods(),
							'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
						)
					);

					if ( $expense->document_id ) {
						// readonly select field.
						eac_form_field(
							array(
								'label'        => __( 'Bill', 'wp-ever-accounting' ),
								'type'         => 'select',
								'name'         => 'bill_id',
								'value'        => $expense->document_id,
								'options'      => array( $expense->document ),
								'option_value' => 'id',
								'option_label' => 'number',
								'disabled'     => true,
							)
						);
						printf('<input type="hidden" name="invoice_id" value="%d">', $expense->document_id);
					}

					eac_form_field(
						array(
							'label'       => __( 'Reference', 'wp-ever-accounting' ),
							'type'        => 'text',
							'name'        => 'reference',
							'value'       => $expense->reference,
							'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						)
					);
					eac_form_field(
						array(
							'label'         => __( 'Note', 'wp-ever-accounting' ),
							'type'          => 'textarea',
							'name'          => 'note',
							'value'         => $expense->note,
							'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
							'wrapper_class' => 'is--full',
						)
					);
					?>
				</div>
			</div>

			<?php
			/**
			 * Fires action to inject custom meta boxes in the main column.
			 *
			 * @param Expense $expense Expense object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_expense_edit_core_meta_boxes', $expense );
			?>
		</div>
		<div class="column-2">

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->expenses->get_statuses(),
							'value'       => $expense->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);

					/**
					 * Fires to add custom actions.
					 *
					 * @param Expense $expense Expense object.
					 *
					 * @since 2.0.0
					 */
					do_action( 'eac_expense_edit_misc_actions', $expense );
					?>
				</div>

				<div class="eac-card__footer">
					<?php if ( $expense->exists() ) : ?>
						<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $expense->get_edit_url() ), 'bulk-expenses' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary"><?php esc_html_e( 'Update Expense', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Expense', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div><!-- .eac-card -->

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Expense $expense Expense object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_expense_edit_side_meta_boxes', $expense );
			?>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_expense' ); ?>
	<input type="hidden" name="action" value="eac_edit_expense"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $expense->id ); ?>"/>
</form>
