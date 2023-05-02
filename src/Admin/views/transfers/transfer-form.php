<?php
/**
 * View: Transfer Form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Vendors
 *
 * @var \EverAccounting\Models\Transfer $transfer Transfer object.
 */

defined( 'ABSPATH' ) || exit();
?>
<form id="eac-transfer-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'account',
						'name'          => 'from_account_id',
						'label'         => __( 'From Account', 'wp-ever-accounting' ),
						'value'         => $transfer->get_from_account_id(),
						'placeholder'   => __( 'Select account', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'account',
						'name'          => 'to_account_id',
						'label'         => __( 'To Account', 'wp-ever-accounting' ),
						'value'         => $transfer->get_to_account_id(),
						'placeholder'   => __( 'Select account', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
						'suffix'        => '<button class="eac-add-account" type="button"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				eac_input_field(
					array(
						'type'          => 'price',
						'name'          => 'amount',
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder'   => '0.00',
						'value'         => $transfer->get_amount(),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'date',
						'name'          => 'date',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'placeholder'   => 'YYYY-MM-DD',
						'value'         => $transfer->get_date(),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'category',
						'name'          => 'category_id',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'value'         => $transfer->get_category_id(),
						'placeholder'   => __( 'Select category', 'wp-ever-accounting' ),
						'required'      => true,
						'subtype'       => 'other',
						'wrapper_class' => 'eac-col-6',
						'suffix'        => '<button class="eac-add-category" type="button" data-type="transfer"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Extra Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'select',
						'name'          => 'payment_method',
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'         => $transfer->get_payment_method(),
						'options'       => eac_get_payment_methods(),
						'placeholder'   => __( 'Select payment method', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'text',
						'name'          => 'reference',
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'value'         => $transfer->get_reference(),
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'textarea',
						'name'          => 'note',
						'label'         => __( 'Notes', 'wp-ever-accounting' ),
						'value'         => $transfer->get_note(),
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-12',
					)
				);
				?>

			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_transfer' ); ?>
	<input type="hidden" name="action" value="eac_edit_transfer">
	<input type="hidden" name="id" value="<?php echo esc_attr( $transfer->get_id() ); ?>">
</form>


