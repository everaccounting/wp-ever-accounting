<?php
/**
 * This class handles building pretty report graphs
 *
 * @since       1.0.2
 * @package     EverAccounting
 */

namespace EverAccounting;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * EAccounting_Graph Class
 *
 * @since 1.0.2
 */
class Chart {
	public $id;
	public $datasets = [];
	public $labels = [];
	public $container = '';
	public $options = [];
	public $type = '';
	public $loader_color = '#22292F';
	public $height = 400;
	public $width = null;

	/**
	 * Stores the dataset class to be used.
	 *
	 * @var object
	 */
	protected $dataset = DataSet::class;

	/**
	 * Chart constructor.
	 */
	public function __construct() {
		$this->id = md5( rand() );
//		$this->options( [
//				'maintainAspectRatio' => false,
//				'scales'              => [
//						'xAxes' => [],
//						'yAxes' => [
//								[
//										'ticks' => [
//												'beginAtZero' => true,
//										],
//								],
//						],
//				],
//		] );
	}


	/**
	 * Set the chart type.
	 *
	 * @param string $type
	 *
	 * @return self
	 */
	public function type( string $type ) {
		$this->type = $type;

		return $this;
	}

	/**
	 * Set the chart height.
	 *
	 * @param int $height
	 *
	 * @return self
	 */
	public function height( int $height ) {
		$this->height = $height;

		return $this;
	}

	/**
	 * Set the chart width.
	 *
	 * @param int $width
	 *
	 * @return self
	 */
	public function width( int $width ) {
		$this->width = $width;

		return $this;
	}

	/**
	 * Set the chart options.
	 *
	 * @param array $options
	 * @param bool  $overwrite
	 *
	 * @return self
	 */
	public function options( $options, bool $overwrite = false ) {
		if ( $overwrite ) {
			$this->options = $options;
		} else {
			$this->options = array_replace_recursive( $this->options, $options );
		}

		return $this;
	}

	/**
	 * Set the chart labels.
	 *
	 * @param array $labels
	 *
	 * @return self
	 */
	public function labels( $labels ) {

		$this->labels = $labels;

		return $this;
	}

	/**
	 * Adds a new dataset to the chart.
	 *
	 * @param array $dataset
	 *
	 * @return self
	 */
	public function dataset( $dataset ) {
		$dataset = wp_parse_args( $dataset, array(
			'label'           => '',
			'data'            => array(),
			'color'           => '',
			'backgroundColor' => '',
			'options'         => array(),
			'fill'            => false,
		) );
		array_push( $this->datasets, $dataset );

		return $this;
	}

	/**
	 * Set line chart options.
	 *
	 * @since 1.0.2
	 * @return $this
	 */
	public function set_line_options() {
		$this->options( [
			'tooltips'   => [
				'backgroundColor' => '#000000',
				'titleFontColor'  => '#ffffff',
				'bodyFontColor'   => '#e5e5e5',
				'bodySpacing'     => 4,
				'YrPadding'       => 12,
				'mode'            => 'nearest',
				'intersect'       => 0,
				'position'        => 'nearest',
			],
			'responsive' => true,
			'scales'     => [
				'yAxes' => [
					[
						'barPercentage' => 1.6,
						'ticks'         => [
							'padding'   => 10,
							'fontColor' => '#9e9e9e',
						],
						'gridLines'     => [
							'drawBorder'       => false,
							'color'            => 'rgba(29,140,248,0.1)',
							'zeroLineColor'    => 'transparent',
							'borderDash'       => [ 2 ],
							'borderDashOffset' => [ 2 ],
						],
					]
				],
				'xAxes' => [
					[
						'barPercentage' => 1.6,
						'ticks'         => [
							'suggestedMin' => 60,
							'suggestedMax' => 125,
							'padding'      => 20,
							'fontColor'    => '#9e9e9e',
						],
						'gridLines'     => [
							'drawBorder'    => false,
							'color'         => 'rgba(29,140,248,0.0)',
							'zeroLineColor' => 'transparent',
						],
					]
				],
			],
		] );

		return $this;
	}

	/**
	 * Set donut options.
	 *
	 * @since 1.0.2
	 *
	 * @param array $colors
	 *
	 * @return $this
	 */
	public function set_donut_options( $colors ) {
		$this->options( [
			'color'            => array_values( $colors ),
			'cutoutPercentage' => 50,
			'legend'           => [
				'position' => 'right',
			],
			'tooltips'         => [
				'backgroundColor' => '#000000',
				'titleFontColor'  => '#ffffff',
				'bodyFontColor'   => '#e5e5e5',
				'bodySpacing'     => 4,
				'xPadding'        => 12,
				'mode'            => 'nearest',
				'intersect'       => 0,
				'position'        => 'nearest',
			],
			'scales'           => [
				'yAxes' => [
					'display' => false,
				],
				'xAxes' => [
					'display' => false,
				],
			],
		] );

		return $this;
	}

	/**
	 * Render the chart.
	 *
	 * @since 1.0.2
	 */
	public function render() {
		$chart = json_encode( array(
			'type'    => $this->type,
			'data'    => array(
				'labels'   => $this->labels,
				'datasets' => $this->datasets,
			),
			'options' => $this->options
		) );
		eaccounting_enqueue_js( "new Chart(document.getElementById('ea-chart-$this->id'),$chart);" );
		echo sprintf( '<canvas id="ea-chart-%s" height="%s" width="%s">',
			$this->id,
			$this->height,
			$this->width );
	}
}