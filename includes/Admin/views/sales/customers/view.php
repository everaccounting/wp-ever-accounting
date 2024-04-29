<?php
/**
 * View for the customer details.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @subpackage Admin/Views/Sales/Customers
 * @var $customer \EverAccounting\Models\Customer
 */

defined( 'ABSPATH' ) || exit();
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'TBD', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'view' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
<div class="bkit-row">
	<div class="bkit-col-3">
		<div class="bkit-card">
			<div class="bkit-card__header">
				Customer Details
			</div>
		</div>

		<div class="bkit-list has--border has--split has--hover is--small">
			<h2 class="bkit-list__header">Header</h2>
			<div class="bkit-list__item">
				<div>
					<img src="https://via.placeholder.com/54" alt="Placeholder" />
					<span>Item</span>
				</div>
				<span>Value</span>
			</div>
			<div class="bkit-list__item">
				<div>
					<img src="https://via.placeholder.com/54" alt="Placeholder" />
					<span>Item</span>
				</div>
				<span>Value</span>
			</div>
		</div>
	</div>
	<div class="bkit-col-9">
		<ul class="eac-summaries">
			<li class="eac-summary">
				<div class="eac-summary__label">Net Sales</div>
				<div class="eac-summary__data">
					<div class="eac-summary__value">$0.00</div>
				</div>
				<div class="eac-summary__legend">This Month</div>
			</li>
			<li class="eac-summary">
				<div class="eac-summary__label">Net Expenses</div>
				<div class="eac-summary__data">
					<div class="eac-summary__value">$0.00</div>
				</div>
				<div class="eac-summary__legend">This Month</div>
			</li>
			<li class="eac-summary">
				<div class="eac-summary__label">Net Profit</div>
				<div class="eac-summary__data">
					<div class="eac-summary__value">$0.00</div>
				</div>
				<div class="eac-summary__legend">This Month</div>
			</li>
		</ul>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius, magni!
	</div>
</div>
