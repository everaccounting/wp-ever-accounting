<?php
/**
 * Sales payments page.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 * @subpackage Admin/Views/Reports
 */

defined( 'ABSPATH' ) || exit;

$datasets = array();
$currency = eac_get_currency();
$years    = range( wp_date( 'Y' ), 2015 );

// TODO: Need to update these bellow two php variables with the dynamic values.
//$year     = wp_date( 'Y' ); // eac_get_input_var( 'year', wp_date( 'Y' ) );
//// $data     = eac_get_payment_report( $year );
//$data = array(
//	"total_amount" => 0,
//	"total_count" => 0,
//	"daily_avg" => 0,
//	"month_avg" => 0,
//	"date_count" => 122,
//	"months" => array(
//		"Jan, 24" => 0,
//		"Feb, 24" => 0,
//		"Mar, 24" => 0,
//		"Apr, 24" => 0,
//		"May, 24" => 0,
//	),
//  	"categories" => array(),
//);
//
//$labels   = array_keys( $data['months'] );
//foreach ( $data['categories'] as $category_id => $datum ) {
//	if ( ! isset( $datasets[ $category_id ] ) ) {
//		$term                     = eac_get_category( $category_id );
//		$term_name                = $term && $term->get_name() ? esc_html( $term->get_name() ) : esc_html__( 'Uncategorized', 'wp-ever-accounting' );
//		$datasets[ $category_id ] = array(
//			'label'           => $term_name,
//			'backgroundColor' => eac_get_random_color( $term_name ),
//		);
//	}
//	$datasets[ $category_id ]['data'] = array_values( $datum );
//}
//$datasets['total'] = array(
//	'type'            => 'line',
//	'fill'            => false,
//	'label'           => esc_html__( 'Total', 'wp-ever-accounting' ),
//	'backgroundColor' => '#3644ff',
//	'borderColor'     => '#3644ff',
//	'data'            => array_values( $data['months'] ),
//);
?>

<div class="bkit-panel">
	<div class="bkit-panel-inner tw-flex tw-justify-between tw-items-center">
		<h3 class="bkit-panel__title">
			<?php echo esc_html__( 'Payment Report', 'wp-ever-accounting' ); ?>
		</h3>
		<form class="eac-report-filters" method="get" action="">
			<select name="year" class="bkit-select">
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

<div class="eac-stats">
	<div class="eac-stat eac-stat__has_footer">
		<div class="eac-stat__header">
			<h3 class="eac-stat__title"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="eac-stat__body eac-stat__has_graph">
			<div class="eac-stat__data">
				<div class="eac-stat__value">
					<span>$987.34</span>
				</div>
				<div class="eac-stat__delta is-success">
					<span>+5%</span>
				</div>
			</div>
			<div class="eac-stat__graph">
				<svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M58.1279 32C58.1279 35.8174 57.2914 39.5884 55.6772 43.0478C54.063 46.5072 51.7105 49.5708 48.785 52.0232C45.8595 54.4756 42.4321 56.2571 38.744 57.2425C35.056 58.2278 31.1968 58.393 27.438 57.7265C23.6792 57.06 20.1121 55.5779 16.9877 53.3845C13.8633 51.1912 11.2575 48.3398 9.3536 45.031C7.44969 41.7223 6.29394 38.0365 5.96768 34.2331C5.64141 30.4296 6.15254 26.6009 7.46512 23.0162L19.245 27.3295C18.5626 29.1931 18.2969 31.1836 18.4665 33.1609C18.6361 35.1382 19.237 37.0543 20.2268 38.7745C21.2165 40.4946 22.5712 41.9769 24.1955 43.1172C25.8198 44.2575 27.6742 45.028 29.6283 45.3745C31.5824 45.721 33.5887 45.6351 35.506 45.1228C37.4233 44.6106 39.2051 43.6844 40.726 42.4095C42.2469 41.1346 43.4699 39.5419 44.3091 37.7434C45.1483 35.945 45.5831 33.9845 45.5831 32H58.1279Z" fill="#0366D6"/>
					<path d="M6.7618 25.2376C8.41404 19.0714 12.2666 13.7234 17.5921 10.2033C22.9177 6.68318 29.3477 5.23458 35.6682 6.13097C41.9887 7.02736 47.7624 10.2067 51.8991 15.0687C56.0359 19.9308 58.2495 26.1391 58.1221 32.5216L45.5799 32.2712C45.6461 28.9531 44.4953 25.7256 42.3447 23.1979C40.1941 20.6703 37.1926 19.0174 33.9067 18.5514C30.6208 18.0854 27.2781 18.8385 24.5095 20.6685C21.7409 22.4985 19.738 25.2788 18.8791 28.4844L6.7618 25.2376Z" fill="#F7A23B"/>
				</svg>
				<!-- <canvas id="eac-stat-value-graph"></canvas>-->
			</div>
		</div>
		<div class="eac-stat__footer">
			<ul class="eac-stat__items">
				<li class="eac-stat__item is-warning">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Upcoming <span class="eac-tooltip" title="Upcoming amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
				<li class="eac-stat__item is-success">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Received <span class="eac-tooltip" title="Received amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="eac-stat eac-stat__has_footer">
		<div class="eac-stat__header">
			<h3 class="eac-stat__title"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="eac-stat__body eac-stat__has_graph">
			<div class="eac-stat__data">
				<div class="eac-stat__value">
					<span>$987.34</span>
				</div>
				<div class="eac-stat__delta is-success">
					<span>+5%</span>
				</div>
			</div>
			<div class="eac-stat__graph">
				<svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M58.1279 32C58.1279 35.8174 57.2914 39.5884 55.6772 43.0478C54.063 46.5072 51.7105 49.5708 48.785 52.0232C45.8595 54.4756 42.4321 56.2571 38.744 57.2425C35.056 58.2278 31.1968 58.393 27.438 57.7265C23.6792 57.06 20.1121 55.5779 16.9877 53.3845C13.8633 51.1912 11.2575 48.3398 9.3536 45.031C7.44969 41.7223 6.29394 38.0365 5.96768 34.2331C5.64141 30.4296 6.15254 26.6009 7.46512 23.0162L19.245 27.3295C18.5626 29.1931 18.2969 31.1836 18.4665 33.1609C18.6361 35.1382 19.237 37.0543 20.2268 38.7745C21.2165 40.4946 22.5712 41.9769 24.1955 43.1172C25.8198 44.2575 27.6742 45.028 29.6283 45.3745C31.5824 45.721 33.5887 45.6351 35.506 45.1228C37.4233 44.6106 39.2051 43.6844 40.726 42.4095C42.2469 41.1346 43.4699 39.5419 44.3091 37.7434C45.1483 35.945 45.5831 33.9845 45.5831 32H58.1279Z" fill="#0366D6"/>
					<path d="M6.7618 25.2376C8.41404 19.0714 12.2666 13.7234 17.5921 10.2033C22.9177 6.68318 29.3477 5.23458 35.6682 6.13097C41.9887 7.02736 47.7624 10.2067 51.8991 15.0687C56.0359 19.9308 58.2495 26.1391 58.1221 32.5216L45.5799 32.2712C45.6461 28.9531 44.4953 25.7256 42.3447 23.1979C40.1941 20.6703 37.1926 19.0174 33.9067 18.5514C30.6208 18.0854 27.2781 18.8385 24.5095 20.6685C21.7409 22.4985 19.738 25.2788 18.8791 28.4844L6.7618 25.2376Z" fill="#F7A23B"/>
				</svg>
				<!-- <canvas id="eac-stat-value-graph"></canvas>-->
			</div>
		</div>
		<div class="eac-stat__footer">
			<ul class="eac-stat__items">
				<li class="eac-stat__item is-warning">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Upcoming <span class="eac-tooltip" title="Upcoming amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
				<li class="eac-stat__item is-success">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Received <span class="eac-tooltip" title="Received amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="eac-stat eac-stat__has_footer">
		<div class="eac-stat__header">
			<h3 class="eac-stat__title"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="eac-stat__body eac-stat__has_graph">
			<div class="eac-stat__data">
				<div class="eac-stat__value">
					<span>$987.34</span>
				</div>
				<div class="eac-stat__delta is-success">
					<span>+5%</span>
				</div>
			</div>
			<div class="eac-stat__graph">
				<svg width="50" height="50" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M58.1279 32C58.1279 35.8174 57.2914 39.5884 55.6772 43.0478C54.063 46.5072 51.7105 49.5708 48.785 52.0232C45.8595 54.4756 42.4321 56.2571 38.744 57.2425C35.056 58.2278 31.1968 58.393 27.438 57.7265C23.6792 57.06 20.1121 55.5779 16.9877 53.3845C13.8633 51.1912 11.2575 48.3398 9.3536 45.031C7.44969 41.7223 6.29394 38.0365 5.96768 34.2331C5.64141 30.4296 6.15254 26.6009 7.46512 23.0162L19.245 27.3295C18.5626 29.1931 18.2969 31.1836 18.4665 33.1609C18.6361 35.1382 19.237 37.0543 20.2268 38.7745C21.2165 40.4946 22.5712 41.9769 24.1955 43.1172C25.8198 44.2575 27.6742 45.028 29.6283 45.3745C31.5824 45.721 33.5887 45.6351 35.506 45.1228C37.4233 44.6106 39.2051 43.6844 40.726 42.4095C42.2469 41.1346 43.4699 39.5419 44.3091 37.7434C45.1483 35.945 45.5831 33.9845 45.5831 32H58.1279Z" fill="#0366D6"/>
					<path d="M6.7618 25.2376C8.41404 19.0714 12.2666 13.7234 17.5921 10.2033C22.9177 6.68318 29.3477 5.23458 35.6682 6.13097C41.9887 7.02736 47.7624 10.2067 51.8991 15.0687C56.0359 19.9308 58.2495 26.1391 58.1221 32.5216L45.5799 32.2712C45.6461 28.9531 44.4953 25.7256 42.3447 23.1979C40.1941 20.6703 37.1926 19.0174 33.9067 18.5514C30.6208 18.0854 27.2781 18.8385 24.5095 20.6685C21.7409 22.4985 19.738 25.2788 18.8791 28.4844L6.7618 25.2376Z" fill="#F7A23B"/>
				</svg>
				<!-- <canvas id="eac-stat-value-graph"></canvas>-->
			</div>
		</div>
		<div class="eac-stat__footer">
			<ul class="eac-stat__items">
				<li class="eac-stat__item is-warning">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Upcoming <span class="eac-tooltip" title="Upcoming amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
				<li class="eac-stat__item is-success">
					<div class="eac-stat__item_data">
						<div class="eac-stat__item_label">
							Received <span class="eac-tooltip" title="Received amount.">[?]</span>
						</div>
						<div class="eac-stat__item_value">
							$0.00
						</div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>

<!--<ul class="eac-summaries">-->
<!--	<li class="eac-summary">-->
<!--		<div class="eac-summary__label">--><?php //esc_html_e( 'Total Payment', 'wp-ever-accounting' ); ?><!--</div>-->
<!--		<div class="eac-summary__data">-->
<!--			<div class="eac-summary__value">--><?php //echo esc_html( eac_format_amount( $data['total_amount'] ) ); ?><!--</div>-->
<!--		</div>-->
<!--	</li>-->
<!--	<li class="eac-summary">-->
<!--		<div class="eac-summary__label">--><?php //esc_html_e( 'Per Month', 'wp-ever-accounting' ); ?><!--</div>-->
<!--		<div class="eac-summary__data">-->
<!--			<div class="eac-summary__value">--><?php //echo esc_html( eac_format_amount( $data['month_avg'] ) ); ?><!--</div>-->
<!--		</div>-->
<!--	</li>-->
<!--	<li class="eac-summary">-->
<!--		<div class="eac-summary__label">--><?php //esc_html_e( 'Per Day', 'wp-ever-accounting' ); ?><!--</div>-->
<!--		<div class="eac-summary__data">-->
<!--			<div class="eac-summary__value">--><?php //echo esc_html( eac_format_amount( $data['daily_avg'] ) ); ?><!--</div>-->
<!--		</div>-->
<!--	</li>-->
<!--</ul>-->

<div class="bkit-card">
	<div class="bkit-card__header">
		<h3 class="bkit-card__title"><?php esc_html_e( 'Chart', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="bkit-card__body">
		<div class="eac-chart">
			<canvas id="eac-payment-chart" style="min-height: 300px;"></canvas>
		</div>
	</div>
</div>

<div class="bkit-card">
	<div class="bkit-card__header">
		<h3 class="bkit-card__title"><?php esc_html_e( 'Payments by Months', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="bkit-card__body padding-0">
		<div class="bkit-overflow-x">
			<table class="widefat striped eac-report-table border-0">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Month', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( array_keys( $data['months'] ) as $label ) : ?>
						<th><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $data['categories'] ) ) : ?>
					<?php foreach ( $data['categories'] as $category_id => $datum ) : ?>
					<tr>
						<td>
							<?php
							$term      = eac_get_category( $category_id );
							$term_name = $term && $term->get_name() ? esc_html( $term->get_name() ) : esc_html__( 'Uncategorized', 'wp-ever-accounting' );
							echo esc_html( $term_name );
							?>
						</td>
						<?php foreach ( $datum as $value ) : ?>
							<td><?php echo esc_html( eac_format_amount( $value ) ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="<?php echo count( $data['months'] ) + 1; ?>">
							<?php esc_html_e( 'No data found', 'wp-ever-accounting' ); ?>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
				<tfoot>
				<tr>
					<th><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['months'] as $value ) : ?>
						<th><?php echo esc_html( eac_format_amount( $value ) ); ?></th>
					<?php endforeach; ?>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	window.onload = function () {
		var ctx = document.getElementById("eac-payment-chart").getContext('2d');
		var symbol = "<?php echo esc_html( $currency ? $currency->get_symbol() : '' ); ?>";
		var myChart = new Chart(ctx, {
			type: 'bar',
			minHeight: 500,
			data: {
				labels: <?php echo wp_json_encode( array_values( $labels ) ); ?>,
				datasets: <?php echo wp_json_encode( array_values( $datasets ) ); ?>
			},
			options: {
				tooltips: {
					displayColors: true,
					YrPadding: 12,
					backgroundColor: "#000000",
					bodyFontColor: "#e5e5e5",
					bodySpacing: 4,
					intersect: 0,
					mode: "nearest",
					position: "nearest",
					titleFontColor: "#ffffff",
					callbacks: {
						label: function (tooltipItem, data) {
							let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
							let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
							return datasetLabel + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: false,
						gridLines: {
							display: true,
						}
					}],
					yAxes: [{
						stacked: false,
						ticks: {
							beginAtZero: true,
							callback: function (value, index, ticks) {
								return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
							}
						},
						type: 'linear',
						barPercentage: 0.4
					}]
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {display: false},
			}
		});
	}
</script>
