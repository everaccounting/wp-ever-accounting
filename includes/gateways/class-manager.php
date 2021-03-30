<?php
/**
 * Payment Gateways Manager.
 *
 * Loads payment gateways.
 *
 * @package EverAccounting
 */

namespace EverAccounting\Gateways;

defined( 'ABSPATH' ) || exit;

/**
 * Class Manager
 * @package EverAccounting/Gateways
 */
class Manager {
	/**
	 * Payment gateway classes.
	 *
	 * @var array
	 */
	protected $gateways = array();

	/**
	 * Enabled Payment gateway classes.
	 *
	 * @var array
	 */
	protected $active_gateways = array();

	/**
	 * Manager constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_redirect', array( $this, 'process_checkout' ) );
		add_action( 'init', array( $this, 'setup_gateways' ) );
	}

	/**
	 * Process invoice form.
	 */
	public function process_checkout() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
		$nonce  = filter_input( INPUT_POST, 'eaccounting_process_checkout_nonce', FILTER_SANITIZE_STRING );
		$gateway = filter_input( INPUT_POST, 'gateway', FILTER_SANITIZE_STRING );
		if ( empty( $action ) || ! wp_verify_nonce( $nonce, 'eaccounting_process_checkout' ) || empty( $gateway ) || !$this->is_active( $gateway )) {
			return;
		}
		$invoice_id = filter_input( INPUT_POST, 'invoice_id', FILTER_SANITIZE_NUMBER_INT );
		$invoice    = eaccounting_get_invoice( $invoice_id );

		if ( empty( $invoice ) || $invoice->get_key() != filter_input( INPUT_POST, 'payment_key', FILTER_SANITIZE_STRING ) ) {
			return;
		}


		do_action('eaccounting_validate_payment_gateway_'. $gateway, $invoice );
		do_action('eaccounting_process_payment_gateway_'. $gateway, $invoice );

		eaccounting_redirect( $invoice->get_url() );
	}


	public function setup_gateways() {
		$load_gateways = array(
			'EverAccounting\Gateways\Cheque',
			'EverAccounting\Gateways\Paypal',
			'EverAccounting\Gateways\Two_Checkout',
		);

		$enabled = eaccounting_get_option( 'gateways', array() );

		// Filter.
		$load_gateways = apply_filters( 'eaccounting_gateways', $load_gateways );

		// Load gateways in order.
		foreach ( $load_gateways as $gateway ) {
			if ( is_string( $gateway ) && class_exists( $gateway ) ) {
				$gateway = new $gateway();
			}

			// Gateways need to be valid and extend EverAccounting\Abstracts\Gateway.
			if ( ! is_a( $gateway, 'EverAccounting\Abstracts\Gateway' ) ) {
				continue;
			}

			$this->gateways[ $gateway->id ] = $gateway;

			if ( array_key_exists( $gateway->id, $enabled ) && 'yes' === eaccounting_get_option('gateway_'. $gateway->id .'_active') ) {
				$this->active_gateways[ $gateway->id ] = $gateway;
			}
		}

		ksort( $this->gateways );
	}


	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function available_gateways() {
		return $this->gateways;
	}

	/**
	 * Get available gateways.
	 *
	 * @return array
	 */
	public function get_active_gateways() {
		return $this->active_gateways;
	}

	/**
	 * Check if gateway active.
	 *
	 * @param $gateway
	 *
	 * @return bool
	 */
	public function is_active( $gateway ){
		return array_key_exists( $gateway, $this->active_gateways );
	}
}
