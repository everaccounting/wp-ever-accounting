<?php
/**
 * The Template for displaying a payment voucher.
 *
 * This template can be overridden by copying it to yourtheme/eac/content-expense.php
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
 * @var \EverAccounting\Models\Expense $expense
 */

defined( 'ABSPATH' ) || exit;

$company_name = get_option( 'eac_company_name', get_bloginfo( 'name' ) );
$logo         = get_option( 'eac_company_logo', '' );

?>
<div class="eac-panel is-large eac-document is--expense mt-0">
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
				class="eac-document__title-text is-medium"><?php esc_html_e( 'Expense Voucher', 'wp-ever-accounting' ); ?></div>
			<div class="eac-document__title-meta">#<?php echo esc_html( $expense->get_number() ); ?></div>
		</div>
	</div>

	<div class="eac-document__body">
		<div class="eac-document__section document-details">
			<div class="eac-document__from">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'Payment To', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
			</div>
			<div class="eac-document__to">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'Payment by', 'wp-ever-accounting' ); ?></h4>
				<?php if ( $expense->get_customer_id() ) : ?>
					<?php echo wp_kses_post( $expense->get_formatted_address() ); ?>
				<?php else : ?>
					<p class="mt-0"><?php esc_html_e( 'Vendor details not found.', 'wp-ever-accounting' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<div class="eac-document__section document-items">
			<div class="eac-document__subject"></div>
			<table class="eac-document__items">
				<tbody>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Payment Date', 'wp-ever-accounting' ); ?>
					</th>
					<td>
						<?php echo esc_html( $expense->get_date() ); ?>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Payment Method', 'wp-ever-accounting' ); ?>
					</th>
					<td>
						<?php if ( $expense->get_payment_method() && array_key_exists( $expense->get_payment_method(), eac_get_payment_methods() ) ) : ?>
							<?php echo esc_html( eac_get_payment_methods()[ $expense->get_payment_method() ] ); ?>
						<?php else : ?>
							<?php echo esc_html( 'N/A' ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?>
					</th>
					<td>
						<?php if ( $expense->get_reference() ) : ?>
							<?php echo esc_html( $expense->get_reference() ); ?>
						<?php else : ?>
							<?php echo esc_html( 'N/A' ); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="col">
						<?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?>
					</th>
					<td>
						<?php echo esc_html( $expense->get_formatted_amount() ); ?>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php if ( $expense->get_note() ) : ?>
			<div class="eac-document__section document-notes">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( $expense->get_note() ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
