<?php
namespace EverAccounting\Core;

/**
 * Class Cron
 * @package EverAccounting\Core
 */
class Cron{

	/**
	 * Cron constructor.
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'wp', array( $this, 'schedule_events' ) );
	}

	/**
	 * Add more cron schedules.
	 *
	 * @param array $schedules List of WP scheduled cron jobs.
	 *
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'wp-ever-accounting' ),
		);

		$schedules['fifteendays'] = array(
			'interval' => 1296000,
			'display'  => __( 'Every 15 Days', 'wp-ever-accounting' ),
		);

		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'wp-ever-accounting' ),
		);

		return $schedules;
	}

	/**
	 * Create cron jobs.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function schedule_events() {
		if ( ! wp_next_scheduled( 'eaccounting_twicedaily_scheduled_events' ) ) {
			wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', 'eaccounting_twicedaily_scheduled_events' );
		}
		if ( ! wp_next_scheduled( 'eaccounting_daily_scheduled_events' ) ) {
			wp_schedule_event( time() + 10, 'daily', 'eaccounting_daily_scheduled_events' );
		}
		if ( ! wp_next_scheduled( 'eaccounting_weekly_scheduled_events' ) ) {
			wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'weekly', 'eaccounting_weekly_scheduled_events' );
		}
	}
}
