<?php
/**
 * Handles logging for the plugin.
 *
 * @package        EverAccounting
 * @class          EAccounting_Logger
 * @version        1.0.2
 */

namespace EverAccounting\Core;

defined( 'ABSPATH' ) || exit;

/**
 * EAccounting_Logger class.
 */
class Logger {

	/**
	 * @var string
	 */
	const EMERGENCY = 'emergency';
	/**
	 * @var string
	 */
	const ALERT = 'alert';
	/**
	 * @var string
	 */
	const CRITICAL = 'critical';
	/**
	 * @var string
	 */
	const ERROR = 'error';
	/**
	 * @var string
	 */
	const WARNING = 'warning';
	/**
	 * @var string
	 */
	const NOTICE = 'notice';
	/**
	 * @var string
	 */
	const INFO = 'info';
	/**
	 * @var string
	 */
	const DEBUG = 'debug';

	/**
	 * The file handler.
	 *
	 * @since 1.0.2
	 * 
	 * @var null
	 * 
	 */
	protected $handle = null;

	/**
	 * Log messages to be stored later.
	 * 
	 * @since 1.0.2
	 *
	 * @var array
	 */
	protected $cached_logs = array();

	/**
	 * EAccounting_Logger constructor.
	 *
	 * @since 1.0.2
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'write_cached_logs' ) );
	}

	/**
	 * Destructor.
	 *
	 * Cleans up open file handles.
	 *
	 * @since 1.0.2
	 */
	public function __destruct() {
		if ( is_resource( $this->handle ) ) {
            fclose( $this->handle ); // @codingStandardsIgnoreLine.
		}
	}

	/**
	 * Add a log entry.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $level   One of the following:
	 *                        'emergency': System is unusable.
	 *                        'alert': Action must be taken immediately.
	 *                        'critical': Critical conditions.
	 *                        'error': Error conditions.
	 *                        'warning': Warning conditions.
	 *                        'notice': Normal but significant condition.
	 *                        'info': Informational messages.
	 *                        'debug': Debug-level messages.
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 *
	 */
	public function log( $level, $message, $context = array() ) {
		// format log entry
		$time         = date_i18n( 'm-d-Y @ H:i:s' );
		$level_string = strtoupper( $level );
		$entry        = "{$time} {$level_string} {$message}";

		$this->write_log( $entry );
	}

	/**
	 * Add a log entry to chosen file.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $entry Log entry text.
	 *
	 */
	protected function write_log( $entry ) {
		if ( $this->open( $this->handle ) && is_resource( $this->handle ) ) {
            fwrite( $this->handle, $entry . PHP_EOL ); // @codingStandardsIgnoreLine.
		} else {
			$this->cache_log( $entry );
		}
	}

	/**
	 * Cache log to write later.
	 * 
	 * @since 1.0.2
	 * 
	 * @param string $entry Log entry text.
	 *
	 */
	protected function cache_log( $entry ) {
		$this->cached_logs[] = $entry;
	}

	/**
	 * Open log file for writing.
	 *
	 * @since 1.0.2
	 * 
	 * @param resource|null $handle Log handle.
	 * @param string        $name   Optional. Name of the log file.
	 *
	 * @return bool Success.
	 */
	protected function open( $handle, $name = 'eaccounting' ) {
		if ( is_resource( $handle ) ) {
			return true;
		}

		$file = self::get_log_file_path( $name );

		if ( $file ) {
			@wp_mkdir_p( dirname( $file ) );

			if ( ! file_exists( $file ) ) {
                $temphandle = @fopen( $file, 'wb+' ); // @codingStandardsIgnoreLine.
                @fclose( $temphandle ); // @codingStandardsIgnoreLine.

				if ( defined( 'FS_CHMOD_FILE' ) ) {
                    @chmod( $file, FS_CHMOD_FILE ); // @codingStandardsIgnoreLine.
				}
			}

            $resource = @fopen( $file, 'ab' ); // @codingStandardsIgnoreLine.

			if ( $resource ) {
				$this->handle = $resource;

				return true;
			}
		}

		return false;
	}

	/**
	 * Close a handle.
	 *
	 * @since 1.0.2
	 * 
	 * @param resource|string $handle Log handle.
	 *
	 * @return bool success
	 */
	protected function close( $handle ) {
		$result = false;

		if ( is_resource( $handle ) ) {
            $result = fclose( $this->handle ); // @codingStandardsIgnoreLine.
			unset( $this->handle );
		}

		return $result;
	}

	/**
	 * Get a log file path.
	 *
	 * @since 1.0.2
	 * 
	 * @param $name
	 *
	 * @return bool|string The log file path or false if path cannot be determined.
	 * 
	 */
	public static function get_log_file_path( $name ) {
		if ( function_exists( 'wp_hash' ) ) {
			$date_suffix = date( 'Y-m-d', time() );
			$hash_suffix = wp_hash( $name );
			$name        = sanitize_file_name( implode( '-', array( $name, $date_suffix, $hash_suffix ) ) . '.log' );

			return trailingslashit( EACCOUNTING_LOG_DIR ) . $name;
		}

		_doing_it_wrong( __METHOD__, __( 'This method should not be called before plugins_loaded.', 'wp-ever-accounting' ), '1.0.2' );

		return false;
	}

	/**
	 * Write cached logs.
	 *
	 * @since 1.0.2
	 */
	public function write_cached_logs() {
		foreach ( $this->cached_logs as $log ) {
			$this->write_log( $log );
		}
	}

	/**
	 * Clear all logs older than a defined number of days. Defaults to 30 days.
	 *
	 * @since 1.0.2
	 */
	public function clear_expired_logs() {
		$days      = absint( apply_filters( 'eaccounting_logger_days_to_retain_logs', 30 ) );
		$timestamp = strtotime( "-{$days} days" );

		$log_files = self::get_log_files();

		foreach ( $log_files as $log_file ) {
			$last_modified = filemtime( trailingslashit( EACCOUNTING_LOG_DIR ) . $log_file );

			if ( $last_modified < $timestamp ) {
                @unlink( trailingslashit( EACCOUNTING_LOG_DIR ) . $log_file ); // @codingStandardsIgnoreLine.
			}
		}
	}

	/**
	 * Get all log files in the log directory.
	 *
	 * @since 1.0.2
	 * 
	 * @return array
	 */
	public static function get_log_files() {
        $files  = @scandir( EACCOUNTING_LOG_DIR ); // @codingStandardsIgnoreLine.
		$result = array();

		if ( ! empty( $files ) ) {
			foreach ( $files as $key => $value ) {
				if ( ! in_array( $value, array( '.', '..' ), true ) ) {
					if ( ! is_dir( $value ) && strpos( $value, '.log' ) !== false ) {
						$result[ sanitize_title( $value ) ] = $value;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Remove/delete the chosen file.
	 *
	 * @since 1.0.2
	 * 
	 * @param $file_name
	 *
	 * @return bool
	 */
	public function remove( $file_name ) {
		$removed = false;
		$logs    = self::get_log_files();
		$handle  = sanitize_title( $file_name );

		if ( isset( $logs[ $handle ] ) && $logs[ $handle ] ) {
			$file = realpath( trailingslashit( EACCOUNTING_LOG_DIR ) . $logs[ $handle ] );
			if ( 0 === stripos( $file, realpath( trailingslashit( EACCOUNTING_LOG_DIR ) ) ) && is_file( $file ) && is_writable( $file ) ) { // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_is_writable
				$this->close( $file ); // Close first to be certain no processes keep it alive after it is unlinked.
				$removed = unlink( $file ); // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_unlink
			}
			do_action( 'eaccounting_log_remove', $handle, $removed );
		}

		return $removed;
	}


	/**
	 * Adds an emergency level message.
	 *
	 * System is unusable.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function emergency( $message, $context = array() ) {
		$this->log( self::EMERGENCY, $message, $context );
	}

	/**
	 * Adds an alert level message.
	 *
	 * Action must be taken immediately.
	 * Example: Entire website down, database unavailable, etc.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function alert( $message, $context = array() ) {
		$this->log( self::ALERT, $message, $context );
	}

	/**
	 * Adds a critical level message.
	 *
	 * Critical conditions.
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function critical( $message, $context = array() ) {
		$this->log( self::CRITICAL, $message, $context );
	}

	/**
	 * Adds an error level message.
	 *
	 * Runtime errors that do not require immediate action but should typically be logged
	 * and monitored.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function error( $message, $context = array() ) {
		$this->log( self::ERROR, $message, $context );
	}

	/**
	 * Adds a warning level message.
	 *
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not
	 * necessarily wrong.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function warning( $message, $context = array() ) {
		$this->log( self::WARNING, $message, $context );
	}

	/**
	 * Adds a notice level message.
	 *
	 * Normal but significant events.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function notice( $message, $context = array() ) {
		$this->log( self::NOTICE, $message, $context );
	}

	/**
	 * Adds a info level message.
	 *
	 * Interesting events.
	 * Example: User logs in, SQL logs.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function info( $message, $context = array() ) {
		$this->log( self::INFO, $message, $context );
	}

	/**
	 * Adds a debug level message.
	 *
	 * Detailed debug information.
	 *
	 * @since 1.0.2
	 * 
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 *
	 */
	public function debug( $message, $context = array() ) {
		$this->log( self::DEBUG, $message, $context );
	}
}
