<?php
/**
 * Abstract payment gateway
 *
 * Hanldes generic payment gateway functionality which is extended by idividual payment gateways.
 *
 * @class WC_Payment_Gateway
 * @package EverAccounting\Abstracts
 */

namespace EverAccounting\Abstracts;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Gateway
 * @package EverAccounting\Abstracts
 */
abstract class Gateway {
	/**
	 * ID of the gateway.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Yes or no based on whether the method is enabled.
	 *
	 * @var string
	 */
	public $enabled = 'yes';

	/**
	 * Payment method title for the frontend.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Account id of the deposited payment.
	 *
	 * @var int
	 */
	public $account_id;

	/**
	 * Gateway constructor.
	 */
	public function __construct() {
		if ( ! empty( $this->id ) ) {
			add_filter( 'eaccounting_settings_gateways', array( $this, 'register_settings' ) );
			add_action( 'eaccounting_checkout_fields_' . $this->id, array( $this, 'checkout_fields' ) );
			add_action( 'eaccounting_validate_payment_gateway_' . $this->id, array( $this, 'validate_fields' ) );
			add_action( 'eaccounting_process_payment_gateway_' . $this->id, array( $this, 'process_checkout' ) );
		}
	}


	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'eaccounting_gateway_title', $this->title, $this->id );
	}

	/**
	 * Return the title for public view.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return apply_filters( 'eaccounting_gateway_method_title', eaccounting_get_option( $this->id . '_title', $this->title ), $this );
	}

	/**
	 * @param $gateways
	 *
	 * @return mixed
	 */
	public function register_settings( $gateways ) {
		$gateways[ $this->id ] = array(
			'title'  => $this->get_title(),
			'fields' => $this->settings_fields()
		);

		return $gateways;
	}

	/**
	 * Return admin settings fields.
	 * @return array
	 */
	public function settings_fields() {
		return array(
			array(
				'id'    => 'gateway_' . $this->id . '_active',
				'title' => __( 'Active', 'wp-ever-accounting' ),
				'type'  => 'checkbox',
				'desc'  => sprintf( __( 'Enable %s', 'wp-ever-accounting' ), $this->get_title() ),
			),
			array(
				'id'    => 'gateway_default',
				'title' => __( 'Default gateway', 'wp-ever-accounting' ),
				'type'  => 'checkbox',
				'desc'  => __( 'Make this as default gateway.', 'wp-ever-accounting' ),
			),
			array(
				'id'      => 'gateway_' . $this->id . '_title',
				'title'   => __( 'Title', 'wp-ever-accounting' ),
				'tooltip' => __( 'This controls the title which the user sees during payment.', 'wp-ever-accounting' ),
				'default' => $this->get_title()
			),
			array(
				'id'      => 'gateway_' . $this->id . '_description',
				'title'   => __( 'Description', 'wp-ever-accounting' ),
				'tooltip' => esc_html__( 'Payment method description that the customer will see on the payment page.', 'wp-ever-accounting' ),
				'default' => '',
				'type'    => 'textarea'
			)
		);
	}

	/**
	 * If There are no payment fields show the description if set.
	 * Override this in your gateway if you have some.
	 *
	 * @param Invoice $invoice
	 */
	public function checkout_fields( $invoice ) {

	}


	/**
	 * Validate frontend fields.
	 *
	 * Validate payment fields on the frontend.
	 *
	 * @return bool
	 */
	public function validate_fields( $invoice ) {
		return true;
	}

	/**
	 * Process Payment.
	 *
	 * Process the payment. Override this in your gateway. When implemented, this should.
	 * return the success and redirect in an array. e.g:
	 *
	 *        return array(
	 *            'result'   => 'success',
	 *            'redirect' => $this->get_return_url( $order )
	 *        );
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 */
	public function process_checkout( $invoice ) {


		return array();
	}
}
