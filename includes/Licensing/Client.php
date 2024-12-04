<?php

namespace EverAccounting\Licensing;

defined( 'ABSPATH' ) || exit;

/**
 * Client class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Client {

	/**
	 * The API URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const API_URL = 'https://wpeveraccounting.com/edd-sl-api';

	/**
	 * Activate the specified license key.
	 *
	 * @param string $license_key The license key to activate.
	 * @param int    $item_id The download ID for the item to check.
	 * @param string $url The URL to activate.
	 *
	 * @return \stdClass The result object (see above).
	 */
	public function activate( $license_key, $item_id, $url ) {
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_id'    => $item_id,
			'url'        => $url,
		);

		return $this->api_request( $api_params );
	}

	/**
	 * Deactivate the specified license key.
	 *
	 * @param string $license_key The license key to deactivate.
	 * @param int    $item_id The download ID for the item to check.
	 * @param string $url The URL to deactivate.
	 *
	 * @return \stdClass The result object (see above).
	 */
	public function deactivate( $license_key, $item_id, $url ) {
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_id'    => $item_id,
			'url'        => $url,
		);

		return $this->api_request( $api_params );
	}

	/**
	 * Checks the specified license key.
	 *
	 * @param string $license_key The license key to check.
	 * @param int    $item_id The download ID for the item to check.
	 * @param string $url The URL to check.
	 *
	 * @return \stdClass The result object (see above).
	 */
	public function check( $license_key, $item_id, $url ) {
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $license_key,
			'item_id'    => $item_id,
			'url'        => $url,
		);

		return $this->api_request( $api_params );
	}

	/**
	 * Get the latest version.
	 *
	 * @param string $license_key The license key.
	 * @param int    $item_id The item ID.
	 * @param string $slug The plugin slug.
	 * @param bool   $beta_testing Whether to check for beta versions.
	 *
	 * @return \stdClass The result object.
	 */
	public function get_latest_version( $license_key, $item_id, $slug, $beta_testing = false ) {
		$api_params = array(
			'edd_action' => 'get_version',
			'license'    => $license_key,
			'item_id'    => $item_id,
			'slug'       => $slug,
			'beta'       => $beta_testing,
		);

		$result = $this->api_request( $api_params );
		if ( $result->success && is_object( $result->response ) ) {
			foreach ( $result->response as $prop => $data ) {
				$result->response->{$prop} = $data;
			}
		}

		return $result;
	}

	/**
	 * API request.
	 *
	 * @param array $params The API parameters.
	 *
	 * @return \stdClass The result object.
	 */
	private function api_request( $params ) {
		$params = wp_parse_args(
			$params,
			array(
				'url'           => wp_parse_url( home_url(), PHP_URL_HOST ),
				'wp_version'    => get_bloginfo( 'version' ),
				'php_version'   => PHP_VERSION,
				'mysql_version' => $GLOBALS['wpdb']->db_version(),
			)
		);

		$response = wp_remote_post(
			add_query_arg( array( 'url' => rawurlencode( home_url() ) ), self::API_URL ),
			array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => $params,
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} elseif ( wp_remote_retrieve_response_message( $response ) ) {
				$message = wp_remote_retrieve_response_message( $response );
			} else {
				$message = __( 'An error has occurred, please try again.', 'wp-ever-accounting' );
			}

			return (object) array(
				'success'  => false,
				'response' => $message,
			);
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}
