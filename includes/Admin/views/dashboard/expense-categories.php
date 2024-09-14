<?php
/**
 * Expense Categories.
 *
 * Show top 5 expense categories with highest amount.
 *
 * @package EverAccounting\Admin
 * @since   1.1.6
 */

defined( 'ABSPATH' ) || exit;

$data     = eac_get_expenses_report( wp_date( 'Y' ) );
$total    = array_sum( $data['months'] );
$amounts  = array();
$labels   = array();
$percents = array();
foreach ( $data['categories'] as $category_id => $datum ) {
	$category_total           = array_sum( $datum );
	$amounts[ $category_id ]  = $category_total;
	$term                     = eac_get_category( $category_id );
	$term_name                = $term && $term->name ? esc_html( $term->name ) : __( 'Uncategorized', 'wp-ever-accounting' );
	$labels[ $category_id ]   = $term_name;
	$percents[ $category_id ] = $category_total > 0 ? round( ( $category_total / $total ) * 100, 2 ) : 0;
}
arsort( $amounts );
arsort( $percents );
if ( count( $amounts ) > 4 ) {
	$amounts            = array_slice( $amounts, 0, 4, true );
	$amounts['others']  = $total - array_sum( $amounts );
	$labels['others']   = __( 'Others', 'wp-ever-accounting' );
	$percents['others'] = 100 - array_sum( array_slice( $percents, 0, 4, true ) );
}
?>

<div class="eac-card">
	<div class="eac-card__header">
		<?php esc_html_e( 'Top Expense Categories', 'wp-ever-accounting' ); ?>
		<?php if ( ! empty( $amounts ) ) : ?>
			<div class="eac-card__header__actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-reports&tab=expenses' ) ); ?>">
					<?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
	<div class="eac-card__body !tw-p-0">
		<div class="eac-overflow-x">
			<table class="eac-table is--striped">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Percent', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( empty( $amounts ) ) : ?>
					<tr>
						<td colspan="3"><?php esc_html_e( 'No data found.', 'wp-ever-accounting' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $amounts as $index => $amount ) : ?>
						<tr>
							<td><?php echo esc_html( $labels[ $index ] ); ?></td>
							<td><?php echo eac_format_amount( $amount ); ?></td>
							<td><?php echo esc_html( $percents[ $index ] ); ?>%</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
