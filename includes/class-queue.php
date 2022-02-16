<?php
/**
 * Queue
 *
 * @since 1.1.3
 * @package  Ever_Accounting
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Queue
 */
class Queue extends \WP_Background_Process {
	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.3
	 * @var self
	 */
	protected static $instance;

	/**
	 * Main Plugin Instance.
	 *
	 * @since 1.1.3
	 * @return self - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Queue Constructor.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wp_ever_accounting_queue_process';

		parent::__construct();
	}


	/**
	 * Is queue empty.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	protected function is_queue_empty() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return ! ( $count > 0 );
	}


	/**
	 * Get batch.
	 *
	 * @since 1.1.3
	 * @return \stdClass Return the first batch from the queue.
	 */
	protected function get_batch() {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} LIKE %s ORDER BY {$key_column} ASC LIMIT 1", $key ) ); // @codingStandardsIgnoreLine.

		$batch       = new \stdClass();
		$batch->key  = $query->$column;
		$batch->data = array_filter( (array) maybe_unserialize( $query->$value_column ) );

		return $batch;
	}


	/**
	 * Handle.
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 *
	 * @since 1.1.3
	 */
	protected function handle() {
		$this->lock_process();

		do {
			$batch = $this->get_batch();

			foreach ( $batch->data as $key => $value ) {
				$task = $this->task( $value );

				if ( false !== $task ) {
					$batch->data[ $key ] = $task;
				} else {
					unset( $batch->data[ $key ] );
				}

				if ( $this->batch_limit_exceeded() ) {
					// Batch limits reached.
					break;
				}
			}

			// Update or delete current batch.
			if ( ! empty( $batch->data ) ) {
				$this->update( $batch->key, $batch->data );
			} else {
				$this->delete( $batch->key );
			}
		} while ( ! $this->batch_limit_exceeded() && ! $this->is_queue_empty() );

		$this->unlock_process();

		// Start next batch or complete process.
		if ( ! $this->is_queue_empty() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}
	}

	/**
	 * Get memory limit.
	 *
	 * @since 1.1.3
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === (int) $memory_limit ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32G';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}


	/**
	 * Schedule cron healthcheck.
	 *
	 * @param array $schedules Schedules.
	 *
	 * @since 1.1.3
	 * @return array
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		$interval = apply_filters( $this->identifier . '_cron_interval', 5 );

		if ( property_exists( $this, 'cron_interval' ) ) {
			$interval = apply_filters( $this->identifier . '_cron_interval', $this->cron_interval );
		}

		// Adds every 5 minutes to the existing schedules.
		$schedules[ $this->identifier . '_cron_interval' ] = array(
			'interval' => MINUTE_IN_SECONDS * $interval,
			/* translators: %d: interval */
			'display'  => sprintf( __( 'Every %d minutes', 'wp-ever-accounting' ), $interval ),
		);

		return $schedules;
	}

	/**
	 * Schedule fallback event.
	 *
	 * @since 1.1.3
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( time() + 10, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function.
	 *
	 * @since 1.1.3
	 * @return string|bool
	 */
	protected function task( $callback ) {
		$result = false;
		if ( is_callable( $callback ) ) {
			Plugin::log( sprintf( 'Running updater %s callback ', var_export( $callback, true ) ) ); // @codingStandardsIgnoreLine.
			$result = (bool) call_user_func( $callback );

			if ( $result ) {
				Plugin::log( sprintf( '%s callback needs to run again', var_export( $callback, true ) ) ); // @codingStandardsIgnoreLine.
			} else {
				Plugin::log( sprintf( 'Finished running %s callback', var_export( $callback, true ) ) ); // @codingStandardsIgnoreLine.
			}
		} else {
			Plugin::log( sprintf( 'Could not find %s callback', var_export( $callback, true ) ) ); // @codingStandardsIgnoreLine.
		}

		return $result ? $callback : false;
	}

	/**
	 * See if the batch limit has been exceeded.
	 *
	 * @since 1.1.3
	 * @return bool
	 */
	protected function batch_limit_exceeded() {
		return $this->time_exceeded() || $this->memory_exceeded();
	}


	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 *
	 * @since 1.1.3
	 */
	public function kill_process() {
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
		}
	}

	/**
	 * Delete all batches.
	 *
	 * @since 1.1.3
	 * @return self
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Push to queue
	 *
	 * @param string $callback Data.
	 *
	 * @since 1.1.3
	 * @return $this
	 */
	public function add( $callback ) {
		return $this->push_to_queue( $callback );
	}
}
