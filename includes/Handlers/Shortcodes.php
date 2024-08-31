<?php

namespace EverAccounting\Handlers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcodes.
 *
 * @since   1.0.0
 * @package EverAccounting\Controllers
 */
class Shortcodes {

	/**
	 * Shortcodes constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_shortcode( 'eac_revenue', array( $this, 'render_revenue' ) );
		add_shortcode( 'eac_expense', array( $this, 'render_expense' ) );
		add_shortcode( 'eac_invoice', array( $this, 'render_invoice' ) );
		add_shortcode( 'eac_bill', array( $this, 'render_bill' ) );
	}

	/**
	 * Revenue shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @since 1.0.0
	 */
	public function render_revenue( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'eac_revenue'
		);

		$revenue = eac_get_revenue( $atts['id'] );

		if ( ! $revenue ) {
			return '';
		}

		ob_start();
		eac_get_template( 'revenue.php', array( 'revenue' => $revenue ) );
		return ob_get_clean();
	}

	/**
	 * Expense shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @since 1.0.0
	 */
	public function render_expense( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'eac_expense'
		);

		$expense = eac_get_expense( $atts['id'] );

		if ( ! $expense ) {
			return '';
		}

		ob_start();
		eac_get_template( 'expense.php', array( 'expense' => $expense ) );
		return ob_get_clean();
	}

	/**
	 * Invoice shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @since 1.0.0
	 */
	public function render_invoice( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'eac_invoice'
		);

//		$invoice = eac_get_invoice( $atts['id'] );
//
//		if ( ! $invoice ) {
//			return '';
//		}

		ob_start();
		eac_get_template( 'invoice.php', array( 'invoice' => $invoice ) );
		return ob_get_clean();
	}

	/**
	 * Bill shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @since 1.0.0
	 */
	public function render_bill( $atts ) {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'eac_bill'
		);

		$bill = eac_get_bill( $atts['id'] );

		if ( ! $bill ) {
			return '';
		}

		ob_start();
		eac_get_template( 'bill.php', array( 'bill' => $bill ) );
		return ob_get_clean();
	}
}
