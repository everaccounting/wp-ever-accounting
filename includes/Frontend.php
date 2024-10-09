<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Frontend class
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Frontend {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );
		add_action( 'eac_head', array( __CLASS__, 'enqueue_scripts' ) );
		add_action( 'eac_output_payment', array( __CLASS__, 'output_payment' ) );
	}

	/**
	 * Register scripts.
	 *
	 * @since 1.0.0
	 */
	public static function register_scripts() {
		EAC()->scripts->register_script( 'eac-frontend', 'css/frontend.css' );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_scripts() {
		wp_enqueue_style( 'eac-frontend' );
	}


	/**
	 * Output payment.
	 *
	 * @param string $uuid UUID.
	 *
	 * @since 1.1.6
	 */
	public static function output_payment( $uuid ) {
		$payment = EAC()->payments->get( array( 'uuid' => $uuid ) );
		if ( ! $payment ) {
			wp_die( esc_html__( 'Payment not found.', 'ever-accounting' ), 404 );
		}

		eac_header();
		echo do_shortcode( '[eac_payment id="' . $payment->id . '"]' );
		eac_footer();
	}
}
