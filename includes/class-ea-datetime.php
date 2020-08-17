<?php
/**
 * EverAccounting Wrapper for PHP DateTime which adds support for gmt/utc offset when a timezone is absent.
 *
 * @package EverAccounting
 * @since   1.0.2
 *
 */

namespace EAccounting;

use DateTime as Base;

defined( 'ABSPATH' ) || exit;

/**
 * Datetime class.
 *
 * @since   1.0.2
 */
class DateTime extends Base {
    /**
     * UTC Offset, if needed. Only used when a timezone is not set. When
     * timezones are used this will equal 0.
     *
     * @var integer
     * @since   1.0.2
     */
    protected $utc_offset = 0;

    /**
     * Output an ISO 8601 date string in local (WordPress) timezone.
     *
     * @return string
     * @since  1.0.2
     */
    public function __toString() {
        return $this->format( DATE_ATOM );
    }

    /**
     * Set UTC offset - this is a fixed offset instead of a timezone.
     *
     * @param int $offset Offset.
     *
     * @since   1.0.2
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
     * @param \DateTimeZone $timezone DateTimeZone instance.
     *
     * @return DateTime
     * @since   1.0.2
     */
    public function setTimezone( $timezone ) {
        $this->utc_offset = 0;

        return parent::setTimezone( $timezone );
    }

    /**
     * Missing in PHP 5.2 so just here so it can be supported consistently.
     *
     * @return int
     * @since  1.0.2
     */
    public function getTimestamp() {
        return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
    }

    /**
     * Get the timestamp with the WordPress timezone offset added or subtracted.
     *
     * @return int
     * @since  1.0.2
     */
    public function getOffsetTimestamp() {
        return $this->getTimestamp() + $this->getOffset();
    }

    /**
     * Format a date based on the offset timestamp.
     *
     * @param string $format Date format.
     *
     * @return string
     * @since  1.0.2
     */
    public function date( $format ) {
        return gmdate( $format, $this->getOffsetTimestamp() );
    }

    /**
     * Return a localised date based on offset timestamp. Wrapper for date_i18n function.
     *
     * @param string $format Date format.
     *
     * @return string
     * @since  1.0.2
     */
    public function date_i18n( $format = 'Y-m-d' ) {
        return date_i18n( $format, $this->getOffsetTimestamp() );
    }

    /**
     * Return mysql date time.
     *
     * @return string date time
     * @since 1.0.2
     */
    public function date_mysql() {
        return date_i18n( 'Y-m-d H:i:s', $this->getOffsetTimestamp() );
    }
}
