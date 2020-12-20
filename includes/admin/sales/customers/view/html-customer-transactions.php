<?php
$incomes = eaccounting_get_incomes( array( 'customer_id' => $customer->get_id() ) );
if ( empty( $incomes ) ) :
	?>
	<p><?php esc_html_e( 'There is no transactions found.', 'wp-ever-accounting' ); ?></p>
<?php else : ?>
	<table class="widefat striped fixed" style="border: 0;padding-bottom: 20px;">
		<thead>
		<tr>
			<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
			<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
			<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
			<th><?php esc_html_e( 'Account', 'wp-ever-accounting' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $incomes as $income ) : ?>
			<tr>
				<td><?php echo esc_html( $income->get_payment_date() ); ?></td>
				<td><?php echo esc_html( eaccounting_price( $income->get_amount(), $income->get_currency_code() ) ); ?></td>
				<td>
					<?php $category = eaccounting_get_category( $income->get_category_id() ); ?>
					<?php echo empty( $category ) ? '&mdash' : esc_html( $category->get_name() ); ?>
				</td>
				<td>
					<?php $account = eaccounting_get_account( $income->get_account_id() ); ?>
					<?php echo empty( $account ) ? '&mdash' : esc_html( $account->get_name() ); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
endif;
