<?php

namespace EverAccounting\Services;

defined('ABSPATH') || exit;


/**
 * Abstract class Service.
 *
 * Responsible for providing common functionality to all services.
 *
 * @package EverAccounting\Services
 */
abstract class Service {
	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.6
	 * @var self
	 */
	public static $instance;

	/**
	 * Get the instance of the class.
	 *
	 * @since 1.1.6
	 * @return self
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}
}
