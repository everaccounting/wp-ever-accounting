<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;

class Account_Balances extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-account-balances';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-4';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Account Balances', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $wpdb;
		$accounts = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT a.name, a.opening_balance, a.currency_code,
				SUM(CASE WHEN t.type='income' then amount WHEN t.type='expense' then - amount END ) balance
				FROM {$wpdb->prefix}ea_accounts a
				LEFT JOIN {$wpdb->prefix}ea_transactions as t ON t.account_id=a.id
				GROUP BY a.id
				ORDER BY balance DESC
				LIMIT %d",
				5
			)
		);

		foreach ( $accounts as $key => $account ) {
			$total            = $account->balance + $account->opening_balance;
			$account->balance = eaccounting_format_price( $total, $account->currency_code );
			$accounts[ $key ] = $account;
		}

		if ( empty( $accounts ) ) {
			echo sprintf(
				'<p class="ea-overview-widget-notice">%s</p>',
				__( 'There is not accounts.', 'wp-ever-accounting' )
			);

			return;
		}

		?>
		<table class="ea-table">
			<thead>
			<tr>
				<th><?php _e( 'Account', 'wp-ever-accounting' ); ?></th>
				<th><?php _e( 'Balance', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $accounts as $account ) : ?>
				<tr>
					<td><?php echo esc_html( $account->name ); ?></td>
					<td><?php echo esc_html( $account->balance ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
