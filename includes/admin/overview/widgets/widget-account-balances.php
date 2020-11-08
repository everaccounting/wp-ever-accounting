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
		$accounts = eaccounting()
			->query()
			->select( 'a.name, a.opening_balance, a.currency_code' )
			->select( "SUM(CASE WHEN t.type='income' then amount WHEN t.type='expense' then - amount END ) balance" )
			->from( 'ea_accounts a' )
			->left_join( 'ea_transactions as t', 't.account_id', 'a.id' )
			->group_by( 'a.id' )
			->order_by( 'balance', 'DESC' )
			->limit( 5 )
			->get( OBJECT, function ( $item ) {
				$total         = $item->balance + $item->opening_balance;
				$item->balance = eaccounting_format_price( $total, $item->currency_code );

				return $item;
			} );
		if ( empty( $accounts ) ) {
			echo sprintf( '<p class="ea-overview-widget-notice">%s</p>',
				__( 'There is not accounts.', 'wp-ever-accounting' ) );

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
			<?php foreach ( $accounts as $account ): ?>
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
