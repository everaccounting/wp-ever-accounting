<?php
/**
 * The Template for displaying an invoice.
 *
 * This template can be overridden by copying it to yourtheme/eac/invoice.php
 *
 * HOWEVER, on occasion EverAccounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpeveraccounting.com/docs/
 * @package EverAccounting\Templates
 * @version 1.0.0
 *
 * @var \EverAccounting\Models\Invoice $invoice The invoice object.
 */

defined( 'ABSPATH' ) || exit;

$company_name = get_option( 'eac_company_name', get_bloginfo( 'name' ) );
$logo         = get_option( 'eac_company_logo', '' );
$columns      = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
);

// If not collecting tax, remove the tax column.
if ( ! $invoice->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}

?>
<div class="eac-panel eac-document is--invoice !tw-mt-0">
	<div class="eac-document__header">
		<div class="eac-document__logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( $logo ) : ?>
					<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
				<?php else : ?>
					<h2><?php echo esc_html( $company_name ); ?></h2>
				<?php endif; ?>
			</a>
		</div>
		<div class="eac-document__title">
			<div
				class="eac-document__title-text"><?php esc_html_e( 'Invoice', 'wp-ever-accounting' ); ?></div>
			<div class="eac-document__title-meta">#<?php echo esc_html( $invoice->number ); ?></div>
		</div>
	</div>
	<div class="eac-document__body">
		<div class="eac-document__section document-details">
			<div class="eac-document__from">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'From', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
			</div>
			<div class="eac-document__to">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'To', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( $invoice->formatted_billing_address ); ?>
			</div>
			<div class="eac-document__data">
				<div>
					<span><?php esc_html_e( 'Invoice Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->issue_date ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->due_date ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Ref. No', 'wp-ever-accounting' ); ?></span>
					<span>
						<?php
						if ( $invoice->reference ) {
							echo esc_html( substr( $invoice->reference, 0, 20 ) );
						} else {
							esc_html_e( 'N/A', 'wp-ever-accounting' );
						}
						?>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
