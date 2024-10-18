<?php
/**
 * Admin View : Report - Payments
 *
 * @since 1.0.0
 * @subpackage Admin/Views
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;
$years = range( wp_date( 'Y' ), 2015 );
$year  = filter_input( INPUT_GET, 'year', FILTER_VALIDATE_INT ) ?: wp_date( 'Y' );
?>

<div class="eac-card">
	<div class="tw-flex tw-items-center tw-justify-between">
		<h3>
			<?php echo esc_html__( 'Payment Report', 'wp-ever-accounting' ); ?>
		</h3>
		<form class="ea-report-filters" method="get" action="">
			<select name="year"
			<?php foreach ( $years as $y ) : ?>
				<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $y, $year ); ?>>
					<?php echo esc_html( $y ); ?>
				</option>
			<?php endforeach; ?>
			</select>
			<button type="submit" class="button">
				<?php echo esc_html__( 'Submit', 'wp-ever-accounting' ); ?>
			</button>
			<input hidden="hidden" name="page" value="eac-reports"/>
			<input hidden="hidden" name="tab" value="payments"/>
		</form>
	</div>
</div>
