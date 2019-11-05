<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Cache {
	/**
	 * @var
	 */
	protected $cache_key;

	/**
	 * EAccounting_Cache constructor.
	 *
	 * @param $cache_key
	 */
	public function __construct( $cache_key ) {
		$this->cache_key = $cache_key;
	}

	/**
	 * Set cache
	 *
	 * since 1.0.0
	 * @param $key
	 * @param $value
	 * @param string $group
	 *
	 * @return bool
	 */
	public function set_cache( $key, $value, $group = '' ) {
		$key = $this->generate_key($key, $group);
		return set_transient( $key, maybe_serialize($value), 86400 );
	}

	/**
	 * Get cache
	 * since 1.0.0
	 * @param $key
	 * @param string $group
	 *
	 * @return bool|mixed
	 */
	public function get_cache( $key, $group = '' ) {
		$key = $this->generate_key($key, $group);
		$cached = get_transient($key);
		if ( false === $cached ) {
			return false;
		}

		return maybe_unserialize($cached);
	}

	public function delete_cache( $group ) {

	}

	/**
	 * Generate key
	 *
	 * since 1.0.0
	 * @param $key
	 * @param string $group
	 *
	 * @return string
	 */
	protected function generate_key( $key, $group = '' ) {
		if ( ! is_string( $key ) ) {
			$key = md5( serialize( $key ) );
		}

		return sanitize_key( implode( '_', [
			$this->cache_key,
			$group,
			$key
		] ) );
	}

}
