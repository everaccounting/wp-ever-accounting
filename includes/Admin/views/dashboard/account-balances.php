<?php
$accounts = eac_get_accounts(
	array(
		'type'  => 'bank',
		'limit' => 5,
	)
);
?>
<div class="eac-card">
	<div class="eac-card__header">
		<?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?>
		<?php if ( ! empty( $accounts ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts' ) ); ?>" class="eac-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="eac-card__body !tw-p-0">
		<table class="eac-table is--striped">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Account', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Balance', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $accounts ) ) : ?>
				<?php foreach ( $accounts as $account ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&view=' . $account->id ) ); ?>">
								<?php echo esc_html( $account->name ); ?>
							</a>
						</td>
						<td><?php echo esc_html( $account->formatted_balance ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2"><?php esc_html_e( 'No account found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
