<?php
/**
 * View: Edit Invoice
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Invoices
 * @var int $invoice_id Invoice ID.
 */

defined( 'ABSPATH' ) || exit();

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

$invoice         = new Invoice( $invoice_id );
$title           = $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' );
$billing_fields  = array(
	array(
		'label' => __( 'Name', 'wp-ever-accounting' ),
		'name'  => 'billing_name',
		'value' => '',
	),
	array(
		'label' => __( 'Street', 'wp-ever-accounting' ),
		'name'  => 'billing_street',
		'value' => '',
	),
	array(
		'label' => __( 'City', 'wp-ever-accounting' ),
		'name'  => 'billing_city',
		'value' => '',
	),
	array(
		'label' => __( 'State', 'wp-ever-accounting' ),
		'name'  => 'billing_state',
		'value' => '',
	),
	array(
		'label' => __( 'Zip Code', 'wp-ever-accounting' ),
		'name'  => 'billing_zip',
		'value' => '',
	),
	array(
		'type'  => 'country',
		'label' => __( 'Country', 'wp-ever-accounting' ),
		'name'  => 'billing_country',
		'value' => '',
	),
	array(
		'label' => __( 'Email', 'wp-ever-accounting' ),
		'name'  => 'billing_email',
		'value' => '',
	),
	array(
		'label' => __( 'Phone', 'wp-ever-accounting' ),
		'name'  => 'billing_phone',
		'value' => '',
	),
	array(
		'label' => __( 'VAT Number', 'wp-ever-accounting' ),
		'name'  => 'billing_vat_number',
		'value' => '',
	),
);
$shipping_fields = array(
	array(
		'label' => __( 'Name', 'wp-ever-accounting' ),
		'name'  => 'shipping_name',
		'value' => '',
	),
	array(
		'label' => __( 'Street', 'wp-ever-accounting' ),
		'name'  => 'shipping_street',
		'value' => '',
	),
	array(
		'label' => __( 'City', 'wp-ever-accounting' ),
		'name'  => 'shipping_city',
		'value' => '',
	),
	array(
		'label' => __( 'State', 'wp-ever-accounting' ),
		'name'  => 'shipping_state',
		'value' => '',
	),
	array(
		'type'  => 'country',
		'label' => __( 'Country', 'wp-ever-accounting' ),
		'name'  => 'shipping_country',
		'value' => '',
	),
	array(
		'label' => __( 'Email', 'wp-ever-accounting' ),
		'name'  => 'shipping_email',
		'value' => '',
	),
	array(
		'label' => __( 'Phone', 'wp-ever-accounting' ),
		'name'  => 'shipping_phone',
		'value' => '',
	),
);
?>
<div class="eac-section-header">
	<div>
		<h2><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php if ( $invoice->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&delete=' . $invoice->get_id() ), 'bulk-accounts' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
			<!--view-->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=view&invoice_id=' . $invoice->get_id() ) ); ?>">
				<?php esc_html_e( 'View', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Invoice', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-invoice-form' ) ); ?>
	</div>
</div>

<div class="eac-document-wrapper">
	<form action="">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Invoice Details', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<div class="eac-columns">
					<div class="eac-col-6">
						<?php
						eac_input_field(
							array(
								'label'    => __( 'Customer', 'wp-ever-accounting' ),
								'type'     => 'customer',
								'name'     => 'customer_id',
								'value'    => '',
								'disabled' => true,
								'readonly' => true,
							)
						);
						?>
						<div class="eac-columns">
							<div class="eac-col-6">
								<h3>Billing</h3>
								<?php
								foreach ( $billing_fields as $field ) {
									eac_input_field( $field );
								}
								?>
							</div>
							<div class="eac-col-6">
								<h3>Shipping</h3>
								<?php foreach ( $shipping_fields as $field ) { ?>
									<?php eac_input_field( $field ); ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="eac-col-6">
						<div class="eac-columns">
							<div class="eac-col-6">
								<?php
								eac_input_field(
									array(
										'type'     => 'text',
										'label'    => 'Invoice Number',
										'name'     => 'invoice_number',
										'readonly' => true,
										'value'    => '123',
									)
								);
								// issue date.
								eac_input_field(
									array(
										'type'     => 'text',
										'label'    => 'Invoice Number',
										'name'     => 'invoice_number',
										'readonly' => true,
										'value'    => '123',
									)
								);
								?>
							</div>
							<div class="eac-col-6">
								<?php
								eac_input_field(
									array(
										'type'     => 'text',
										'label'    => 'Invoice Number',
										'name'     => 'invoice_number',
										'readonly' => true,
										'value'    => '123',
									)
								);
								// issue date.
								eac_input_field(
									array(
										'type'     => 'text',
										'label'    => 'Invoice Number',
										'name'     => 'invoice_number',
										'readonly' => true,
										'value'    => '123',
									)
								);
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="eac-card__separator"></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illo, mollitia.
			</div>
			<div class="eac-card__separator"></div>
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Invoice Notes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">

			</div>
			<div class="eac-card__separator"></div>
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Invoice Footer', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">

			</div>
		</div>
	</form>
</div>
