<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Shortcodes
 *
 * @package EverAccounting
 * @since   1.1.5
 */
class Shortcodes extends Singleton {

	/**
	 * Shortcodes constructor.
	 */
	protected function __construct() {
		add_shortcode( 'eac_payment', array( __CLASS__, 'output_payment' ) );
		add_shortcode( 'eac_expense', array( __CLASS__, 'output_expense' ) );
		add_shortcode( 'eac_invoice', array( __CLASS__, 'output_invoice' ) );
		add_shortcode( 'eac_receipt', array( __CLASS__, 'output_bill' ) );
	}

	/**
	 * Payment shortcode.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string
	 * @since 1.1.5
	 */
	public static function output_payment( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts );

		$payment = eac_get_payment( $atts['id'] );
		if ( ! $payment ) {
			return '';
		}
		wp_enqueue_style( 'eac-public-style' );
		ob_start();
		eac_get_template( 'content-payment.php', array(
			'payment' => $payment,
		) );

		return ob_get_clean();
	}

	/**
	 * Expense shortcode.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string
	 * @since 1.1.5
	 */
	public static function output_expense( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts );

		$expense = eac_get_expense( $atts['id'] );
		if ( ! $expense ) {
			return '';
		}
		wp_enqueue_style( 'eac-public-style' );
		ob_start();
		eac_get_template( 'content-expense.php', array(
			'expense' => $expense,
		) );

		return ob_get_clean();
	}

	/**
	 * Invoice shortcode.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string
	 * @since 1.1.5
	 */
	public static function output_invoice( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts );

		$invoice = eac_get_invoice( $atts['id'] );
		if ( ! $invoice ) {
			return '';
		}
		wp_enqueue_style( 'eac-public-style' );
		ob_start();
		eac_get_template( 'content-invoice.php', array(
			'invoice' => $invoice,
		) );

		return ob_get_clean();
	}

	/**
	 * Bill shortcode.
	 *
	 * @param array $atts Attributes.
	 *
	 * @return string
	 * @since 1.1.5
	 */
	public static function output_bill( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts );

		$bill = eac_get_bill( $atts['id'] );
		if ( ! $bill ) {
			return '';
		}
		wp_enqueue_style( 'eac-public-style' );
		ob_start();
		eac_get_template( 'content-bill.php', array(
			'bill' => $bill,
		) );

		return ob_get_clean();
	}
}
