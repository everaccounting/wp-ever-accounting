<?php
/**
 * Dashboard Widget Base class.
 * Provides a base structure for overview content meta boxes.
 *
 * @package     EverAccounting
 * @subpackage  Abstracts
 * @class       Widget
 * @version     1.0.2
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class Widget {
	/**
	 * The ID of the widget. Must be unique.
	 *
	 * @since   1.0.2
	 * @var     string $widget_id The ID of the widget
	 */
	public $widget_id = 'widget';

	/**
	 * Widget width.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-6';

	/**
	 * Overview_Widget constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_add_overview_widget', array( $this, 'add_widget' ) );
	}

	/**
	 * Return widget id.
	 *
	 * @since 1.0.2
	 * @return mixed|void
	 */
	public function get_widget_id() {
		return apply_filters( 'eaccounting_overview_widget_id', 'ea-overview-widget-' . $this->widget_id );
	}

	/**
	 * Return widget's column count.
	 *
	 * @since 1.0.2
	 * @return mixed|void
	 */
	public function get_widget_size() {
		return apply_filters( 'eaccounting_overview_widget_sizes', $this->widget_size, $this->widget_id );
	}

	/**
	 * Get extra widget class.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_widget_class() {
		return '';
	}

	/**
	 * Get widget classes.
	 *
	 * @since 1.0.2
	 * @return mixed|void
	 */
	public function get_widget_classes() {
		return apply_filters(
			'eaccounting_overview_widget_classes',
			implode(
				' ',
				array(
					'ea-overview-widget',
					esc_attr( $this->get_widget_size() ),
					esc_attr( $this->get_widget_class() ),
				)
			),
			$this->widget_id
		);
	}

	/**
	 * Render the widget.
	 *
	 * @since 1.0.2
	 */
	public function add_widget() {
		?>
		<div class="<?php echo $this->get_widget_classes(); ?>" id="<?php echo esc_attr( $this->get_widget_id() ); ?>">
			<?php $this->render_header(); ?>
			<div class="ea-overview-widget-body">
				<?php $this->get_content(); ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Render the header of the widget.
	 *
	 * Overwrite this if you do not want to render
	 * header.
	 *
	 * @since 1.0.2
	 */
	public function render_header() {
		?>
		<div class="ea-overview-widget-header">
			<div class="ea-overview-widget-header-left">
				<?php $this->get_title(); ?>
			</div>
			<div class="ea-overview-widget-header-right">
				<?php $this->get_tools(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return the title of the widget.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function get_title() {

	}

	/**
	 * Render the tools.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function get_tools() {

	}

	/**
	 * Render content.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	abstract public function get_content();

	/**
	 * @since 1.0.2
	 * @throws \Exception
	 * @return array
	 */
	public function get_dates() {
		$financial_start = eaccounting_get_financial_start();
		if ( ( $year_start = date( 'Y-01-01' ) ) !== $financial_start ) {
			$year_start = $financial_start;
		}

		$start_date = empty( $_GET['start_date'] ) ? $year_start : eaccounting_clean( $_GET['start_date'] );
		$end_date   = empty( $_GET['end_date'] ) ? null : eaccounting_clean( $_GET['end_date'] );
		$start      = eaccounting_string_to_datetime( $start_date );

		if ( empty( $end_date ) ) {
			$start_copy = clone $start;
			$end_date   = $start_copy->add( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' );
		}
		$end = eaccounting_string_to_datetime( $end_date );

		return [
			'start' => $start->format( 'Y-m-d' ),
			'end'   => $end->format( 'Y-m-d' ),
		];
	}
}
