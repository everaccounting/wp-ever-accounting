<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<h1 class="wp-heading-inline ea-mb-10"><?php _e( 'Dashboard', 'wp-ever-accounting' ); ?></h1>

	<div class="ea-row">


		<div class="ea-col-4">
			<div class="ea-summery-box income">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-money"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Incomes</span>
					<span class="ea-summery-box-number"><?php echo eaccounting_price(eaccounting_get_total_income());?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-4">
			<div class="ea-summery-box expense">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-shopping-cart"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Expenses</span>
					<span class="ea-summery-box-number"><?php echo eaccounting_price(eaccounting_get_total_expense());?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-4">
			<div class="ea-summery-box profit">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-heart"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Profit</span>
					<span class="ea-summery-box-number"><?php echo eaccounting_price(eaccounting_get_total_profit());?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Latest Income', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<?php
					$incomes = eaccounting_get_revenues( [ 'per_page' => 5 ] );
					if ( empty( $incomes ) ) {
						echo sprintf( '<p>%s</p>', __( 'No incomes found', 'wp-ever-accounting' ) );
					} else {
						?>
						<table class="ea-table">
							<thead>
							<tr>
								<th><?php _e( 'Date', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Category', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>
							<?php
							foreach ( $incomes as $income ) {
								$item = new EAccounting_Revenue( $income );
								echo sprintf( '<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td></tr>', $item->get_paid_at(), $item->get_category('view'), $item->get_amount('view') );
							} ?>
							<tbody>

							</tbody>

						</table>
						<?php
					} ?>
				</div>
			</div>
		</div>


		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Latest Expense', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<?php
					$payments = eaccounting_get_payments( [ 'per_page' => 5 ] );
					if ( empty( $payments ) ) {
						echo sprintf( '<p>%s</p>', __( 'No expenses found', 'wp-ever-accounting' ) );
					} else {
						?>
						<table class="ea-table">
							<thead>
							<tr>
								<th><?php _e( 'Date', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Category', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>
							<?php
							foreach ( $payments as $payment ) {
								$item = new EAccounting_Payment( $income );
								echo sprintf( '<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td></tr>', $item->get_paid_at(), $item->get_category('view'), $item->get_amount('view') );
							} ?>
							<tbody>

							</tbody>

						</table>
						<?php
					} ?>
				</div>
			</div>
		</div>


	</div>


</div>
