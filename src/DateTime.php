<?php
/**
 * EverAccounting Wrapper for PHP DateTime which adds support for gmt/utc offset when a timezone is absent.
 *
 * @since   1.0.2
 *
 * @package EverAccounting
 */

namespace EverAccounting;

use DateTime as DT;

defined( 'ABSPATH' ) || exit;

/**
 * Datetime class.
 *
 * @since   1.0.2
 */
class DateTime extends DT {
	/**
	 * UTC Offset, if needed. Only used when a timezone is not set. When
	 * timezones are used this will equal 0.
	 *
	 * @since   1.0.2
	 * @var integer
	 */
	protected $utc_offset = 0;

	/**
	 * Output an ISO 8601 date string in local (WordPress) timezone.
	 *
	 * @since  1.0.2
	 * @return string
	 */
	public function __toString() {
		return $this->format( DATE_ATOM );
	}

	/**
	 * Clone the current object.
	 *
	 * @since 1.0.2
	 * @return \EverAccounting\DateTime
	 */
	public function copy() {
		return clone $this;
	}

	/**
	 * Set UTC offset - this is a fixed offset instead of a timezone.
	 *
	 * @since   1.0.2
	 *
	 * @param int $offset Offset.
	 *
	 */
	public function set_utc_offset( $offset ) {
		$this->utc_offset = intval( $offset );
	}

	/**
	 * Get UTC offset if set, or default to the DateTime object's offset.
	 *
	 * @since   1.0.2
	 */
	public function getOffset() {
		return $this->utc_offset ? $this->utc_offset : parent::getOffset();
	}

	/**
	 * Set timezone.
	 *
	 * @since   1.0.2
	 *
	 * @param \DateTimeZone $timezone DateTimeZone instance.
	 *
	 * @return DateTime
	 */
	public function setTimezone( $timezone ) {
		$this->utc_offset = 0;

		return parent::setTimezone( $timezone );
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function addYear( $number = 1 ) {
		$this->add( new \DateInterval( "P{$number}Y" ) );
		return $this;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function addMonth( $number = 1 ) {
		$this->add( new \DateInterval( "P{$number}M" ) );

		return $this;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function addDay( $number = 1 ) {
		$this->add( new \DateInterval( "P{$number}D" ) );

		return $this;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function subYear( $number = 1 ) {
		$this->sub( new \DateInterval( "P{$number}Y" ) );

		return $this;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function subMonth( $number = 1 ) {
		$this->sub( new \DateInterval( "P{$number}M" ) );

		return $this;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param int $number
	 *
	 * @return $this
	 */
	public function subDay( $number = 1 ) {
		$this->sub( new \DateInterval( "P{$number}D" ) );

		return $this;
	}

	/**
	 * Missing in PHP 5.2 so just here so it can be supported consistently.
	 *
	 * @since  1.0.2
	 * @return int
	 */
	public function getTimestamp() {
		return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
	}

	/**
	 * Get the timestamp with the WordPress timezone offset added or subtracted.
	 *
	 * @since  1.0.2
	 * @return int
	 */
	public function getOffsetTimestamp() {
		return $this->getTimestamp() + $this->getOffset();
	}

	/**
	 * Format a date based on the offset timestamp.
	 *
	 * @since  1.0.2
	 *
	 * @param string $format Date format.
	 *
	 * @return string
	 */
	public function date( $format ) {
		return gmdate( $format, $this->getOffsetTimestamp() );
	}

	/**
	 * Return a localised date based on offset timestamp. Wrapper for date_i18n function.
	 *
	 * @since  1.0.2
	 *
	 * @param string $format Date format.
	 *
	 * @return string
	 */
	public function date_i18n( $format = 'Y-m-d' ) {
		return date_i18n( $format, $this->getOffsetTimestamp() );
	}

	/**
	 * Return mysql date time.
	 *
	 * @since 1.0.2
	 * @return string date time
	 */
	public function date_mysql() {
		return date( 'Y-m-d H:i:s', $this->getOffsetTimestamp() );
	}

	/**
	 * Get quarter
	 *
	 * @since 1.0.2
	 * @return int
	 */
	public function quarter() {
		return ceil( $this->format( 'm' ) / 3 );
	}
}