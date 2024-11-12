<?php

namespace EverAccounting\Addons;

defined( 'ABSPATH' ) || exit;

/**
 * License class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class License {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Addon
	 */
	protected $addon;

	/**
	 * Addon shortname.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $shortname;

	/**
	 * License option.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $option_name;

	/**
	 * License data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'license' => '',
		'status'  => 'invalid',
		'url'     => '',
		'expires' => '',
	);

	/**
	 * Client instance.
	 *
	 * @since 1.0.0
	 * @var Client
	 */
	protected $client;

	/**
	 * License constructor.
	 *
	 * @param Addon $addon The addon.
	 *
	 * @throws \InvalidArgumentException If the addon is not an instance of Addon.
	 * @since 1.0.0
	 */
	public function __construct( $addon ) {
		// if the addon is not an extended class of Addon, throw an exception.
		if ( ! is_subclass_of( $addon, Addon::class ) ) {
			throw new \InvalidArgumentException( 'The addon must be an instance of Addon.' );
		}

		$this->addon       = $addon;
		$this->client      = new Client();
		$this->shortname   = preg_replace( '/[^a-zA-Z0-9]/', '_', strtolower( $this->addon->get_slug() ) );
		$this->option_name = $this->shortname . '_license';
		$this->data        = wp_parse_args( (array) get_option( $this->option_name, array() ), $this->data );
	}

	/**
	 * Check if the license exists or not.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function exists() {
		return empty( $this->data['license'] ) ? false : true;
	}

	/**
	 * Determine if the license is valid.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_valid() {
		return 'valid' === $this->data['status'];
	}

	/**
	 * Determine if the license is expired.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_expired() {
		return 'expired' === $this->get_status();
	}

	/**
	 * Determine if the site is moved.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_site_moved() {
		$active_url = isset( $this->data['url'] ) ? $this->data['url'] : '';
		if ( empty( $active_url ) ) {
			return false;
		}

		$has_moved = $active_url !== $this->cleanup_site_url( home_url() );
		if ( $has_moved && $this->is_valid() ) {
			$this->update_license_data(
				array(
					'status' => 'status',
					'error'  => 'site_inactive',
				)
			);
		}

		return $has_moved;
	}

	/**
	 * Get license data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Get the license key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_key() {
		return array_key_exists( 'license', $this->data ) ? $this->data['license'] : '';
	}

	/**
	 * Get the license status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_status() {
		return array_key_exists( 'status', $this->data ) ? $this->data['status'] : '';
	}

	/**
	 * Get error message.
	 *
	 * @param string $status The status.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_message( $status = '' ) {
		if ( empty( $status ) ) {
			$status = $this->get_status();
		}

		switch ( $status ) {
			case 'expired':
				$message = $this->get_expired_message();
				break;
			case 'revoked':
			case 'disabled':
				$message = $this->get_disabled_message();
				break;
			case 'missing':
				$message = $this->get_missing_message();
				break;
			case 'site_inactive':
				$message = $this->get_inactive_message();
				break;
			case 'invalid':
			case 'invalid_item_id':
			case 'item_name_mismatch':
			case 'key_mismatch':
				$message = sprintf(
				/* translators: the extension name. */
					__( 'This appears to be an invalid license key for %s.', 'wp-ever-accounting' ),
					esc_html( $this->addon->get_name() )
				);
				break;
			case 'no_activations_left':
				$message = $this->get_activation_limit_message();
				break;
			case 'license_not_activable':
				$message = __( 'The key you entered belongs to a bundle, please use the product specific license key.', 'wp-ever-accounting' );
				break;
			default:
				$message = sprintf(
				/* translators: the extension name. */
					__( 'Your license key for %s is invalid.', 'wp-ever-accounting' ),
					esc_html( $this->addon->get_name() )
				);
				break;

		}

		return $message;
	}

	/**
	 * Activate the license.
	 *
	 * @param string $license The license key.
	 *
	 * @since 1.0.0
	 * @return bool Whether the license is activated or not.
	 */
	public function activate( $license ) {
		$license = trim( $license );
		if ( empty( $license ) ) {
			return false;
		}

		$data     = array();
		$host     = wp_parse_url( site_url(), PHP_URL_HOST );
		$response = $this->client->activate( $license, $this->addon->get_item_id(), $host );
		$is_valid = $response->success && $response->license && 'valid' === $response->license;

		// Prepare the data.
		$data['url']     = $host;
		$data['status']  = ! $response->success && ! empty( $response->error ) ? sanitize_key( $response->error ) : sanitize_key( $response->license );
		$data['expires'] = ! empty( $response->expires ) ? $response->expires : '';
		$data['license'] = $is_valid ? $license : '';

		$this->update_license_data( $data );
		set_site_transient( 'update_plugins', null );

		return $is_valid;
	}

	/**
	 * Deactivate the license.
	 *
	 * @since 1.0.0
	 * @return bool Whether the license is deactivated or not.
	 */
	public function deactivate() {
		$license = $this->data['license'];
		if ( empty( $license ) ) {
			return false;
		}

		$response = $this->client->deactivate( $license, $this->addon->get_item_id(), home_url() );
		if ( $response->success ) {
			$this->update_license_data(
				array(
					'status'  => 'inactive',
					'expires' => ! empty( $response->expires ) ? $response->expires : '',
				)
			);
		}
		set_site_transient( 'update_plugins', null );
		return true === $response->success;
	}

	/**
	 * Refresh the license.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function refresh() {
		if ( empty( $this->get_key() ) ) {
			return;
		}

		$data     = array();
		$license  = $this->get_key();
		$host     = wp_parse_url( site_url(), PHP_URL_HOST );
		$response = $this->client->activate( $license, $this->addon->get_item_id(), $host );
		$is_valid = $response->success && $response->license && 'valid' === $response->license;

		// Prepare the data.
		$data['url']     = $host;
		$data['status']  = ! $response->success && ! empty( $response->error ) ? sanitize_key( $response->error ) : sanitize_key( $response->license );
		$data['expires'] = ! empty( $response->expires ) ? $response->expires : '';
		$data['license'] = $is_valid ? $license : '';

		$this->update_license_data( $data );
		set_site_transient( 'update_plugins', null );
	}

	/**
	 * Update the license data.
	 *
	 * @param array $data The license data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function update_license_data( $data ) {
		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->data = wp_parse_args( $data, $this->data );
			update_option( $this->option_name, $this->data );
		}
	}

	/**
	 * Cleanup the site url.
	 *
	 * @param string $url The site url.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function cleanup_site_url( $url ) {
		if ( empty( $url ) ) {
			return $url;
		}
		// To lowercase.
		$url = strtolower( $url );
		// Strip www.
		$url = str_replace( array( '://www.', ':/www.' ), '://', $url );
		// Strip scheme.
		$url = str_replace( array( 'http://', 'https://', 'http:/', 'https:/' ), '', $url );

		// Remove trailing slash.
		return untrailingslashit( $url );
	}

	/**
	 * Gets the message for an expired license.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_expired_message() {
		$expires = ! empty( $this->license_data['expires'] && 'lifetime' !== $this->license_data['expires'] ) ? strtotime( $this->data['expires'] ) : 0;
		$expired = $expires > 0 && $expires > wp_date( 'U' ) && ( $expires - wp_date( 'U' ) < DAY_IN_SECONDS );

		if ( $expired ) {
			return sprintf(
			/* translators: %s: license expiration date */
				__( 'Your license key for %1$s has expired on %1$s.Please renew your license to continue receiving updates and support.', 'wp-ever-accounting' ),
				esc_html( $this->addon->get_name() ),
				date_i18n( get_option( 'date_format' ), $expires )
			);
		}

		return sprintf(
		/* translators: %s: license expiration date */
			__( 'Your license key for %1$s has expired. Please renew your license to continue receiving updates and support.', 'wp-ever-accounting' ),
			esc_html( $this->addon->get_name() )
		);
	}

	/**
	 * Gets the message for a disabled license.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_disabled_message() {
		return sprintf(
		/* translators: %s: license expiration date */
			__( 'Your license key for %1$s has been disabled. Please contact support for assistance.', 'wp-ever-accounting' ),
			esc_html( $this->addon->get_name() )
		);
	}

	/**
	 * Gets the message for a license at its activation limit.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_activation_limit_message() {
		return sprintf(
		/* translators: %s: license expiration date */
			__( 'Your license key for %1$s has reached its activation limit.', 'wp-ever-accounting' ),
			esc_html( $this->addon->get_name() )
		);
	}

	/**
	 * Gets the message for an inactive license.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_inactive_message() {
		return sprintf(
		/* translators: %s: license expiration date */
			__( 'Your license key for %1$s is inactive. Please activate your license to continue receiving updates and support.', 'wp-ever-accounting' ),
			esc_html( $this->addon->get_name() )
		);
	}

	/**
	 * Gets the message for a missing license.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_missing_message() {
		return sprintf(
		/* translators: %s: license expiration date */
			__( 'Your license key for %1$s is missing. Please enter your license key to continue receiving updates and support.', 'wp-ever-accounting' ),
			esc_html( $this->addon->get_name() )
		);
	}
}
