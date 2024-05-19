<?php
/**
 * Summaries.
 *
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$sales_summary     = eac_get_sales_summary();
$purchases_summary = eac_get_purchases_summary();
$profits_summary   = eac_get_profits_summary();
?>

<div class="eac-summaries-section  tw-mb-20">
	<ul class="eac-summaries">
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Net Sales', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $sales_summary['total'] ) ); ?></div>
			</div>
			<div class="eac-summary__legend"><?php esc_html_e( 'This Month', 'wp-ever-accounting' ); ?></div>
		</li>
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Net Expenses', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $purchases_summary['total'] ) ); ?></div>
			</div>
			<div class="eac-summary__legend"><?php esc_html_e( 'This Month', 'wp-ever-accounting' ); ?></div>
		</li>
		<li class="eac-summary">
			<div class="eac-summary__label"><?php esc_html_e( 'Net Profit', 'wp-ever-accounting' ); ?></div>
			<div class="eac-summary__data">
				<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $profits_summary['total'] ) ); ?></div>
			</div>
			<div class="eac-summary__legend"><?php esc_html_e( 'This Month', 'wp-ever-accounting' ); ?></div>
		</li>
	</ul>
</div>
