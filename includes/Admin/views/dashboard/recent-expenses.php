<?php
/**
 * Recent Revenues
 *
 * @package WP Ever Accounting
 * @since   1.1.6
 */

defined( 'ABSPATH' ) || exit;

$expenses = eac_get_expenses(
	array(
		'limit'   => 5,
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);
?>
<div class="bkit-card">
	<div class="bkit-card__header">
		<?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?>
		<?php if ( ! empty( $expenses ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=expenses' ) ); ?>" class="bkit-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="bkit-card__body !tw-p-0">
		<table class="eac-table is--striped">
			<thead>
			<tr>
				<th><?php esc_html_e( '#', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $expenses ) ) : ?>
				<?php foreach ( $expenses as $expense ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=expenses&view=' . $expense->id ) ); ?>">
								<?php echo esc_html( $expense->number ); ?>
							</a>
						</td>
						<td><?php echo esc_html( $expense->date ); ?></td>
						<td><?php echo esc_html( $expense->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2"><?php esc_html_e( 'No expenses found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
