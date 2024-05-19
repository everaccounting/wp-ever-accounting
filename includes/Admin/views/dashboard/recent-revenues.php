<?php
/**
 * Recent Revenues
 *
 * @package WP Ever Accounting
 * @since   1.1.6
 */

defined( 'ABSPATH' ) || exit;

$revenues = eac_get_revenues(
	array(
		'limit'   => 5,
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);
?>
<div class="bkit-card">
	<div class="bkit-card__header">
		<?php esc_html_e( 'Recent Revenues', 'wp-ever-accounting' ); ?>
		<?php if ( ! empty( $revenues ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=revenues' ) ); ?>" class="bkit-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
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
			<?php if ( ! empty( $revenues ) ) : ?>
				<?php foreach ( $revenues as $revenue ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&view=' . $revenue->id ) ); ?>">
								<?php echo esc_html( $revenue->number ); ?>
							</a>
						</td>
						<td><?php echo esc_html( $revenue->date ); ?></td>
						<td><?php echo esc_html( $revenue->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="2"><?php esc_html_e( 'No revenues found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
