<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Actions.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Admin
 */
class Actions {

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_post_eac_edit_item', array( $this, 'handle_edit_item' ) );
		add_action( 'admin_post_eac_edit_category', array( $this, 'handle_edit_category' ) );
		add_action( 'admin_post_eac_edit_currency', array( $this, 'handle_edit_currency' ) );
		add_action( 'admin_post_eac_edit_tax', array( $this, 'handle_edit_tax' ) );
	}

	/**
	 * Edit item.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function handle_edit_item() {
		check_admin_referer( 'eac_edit_item' );
		$referer     = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$price       = isset( $_POST['price'] ) ? floatval( wp_unslash( $_POST['price'] ) ) : 0;
		$cost        = isset( $_POST['cost'] ) ? floatval( wp_unslash( $_POST['cost'] ) ) : 0;
		$category_id = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0;
		$unit        = isset( $_POST['unit'] ) ? sanitize_text_field( wp_unslash( $_POST['unit'] ) ) : '';
		$tax_ids     = isset( $_POST['tax_ids'] ) ? array_map( 'absint', wp_unslash( $_POST['tax_ids'] ) ) : array();
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$item        = eac_insert_item(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'price'       => $price,
				'cost'        => $cost,
				'category_id' => $category_id,
				'unit'        => $unit,
				'tax_ids'     => implode( ',', array_unique( array_filter( $tax_ids ) ) ),
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $item ) ) {
			EAC()->flash->error( $item->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Item saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $item->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit();
	}

	/**
	 * Edit category.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function handle_edit_category() {
		check_admin_referer( 'eac_edit_category' );
		$referer  = wp_get_referer();
		$id       = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$desc     = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$category = eac_insert_category(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $category ) ) {
			EAC()->flash->error( $category->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $category->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit currency.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function handle_edit_currency() {
		check_admin_referer( 'eac_edit_currency' );
		$referer            = wp_get_referer();
		$id                 = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$code               = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';
		$name               = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$symbol             = isset( $_POST['symbol'] ) ? sanitize_text_field( wp_unslash( $_POST['symbol'] ) ) : '';
		$exchange_rate      = isset( $_POST['exchange_rate'] ) ? doubleval( wp_unslash( $_POST['exchange_rate'] ) ) : 0;
		$thousand_separator = isset( $_POST['thousand_separator'] ) ? sanitize_text_field( wp_unslash( $_POST['thousand_separator'] ) ) : '';
		$decimal_separator  = isset( $_POST['decimal_separator'] ) ? sanitize_text_field( wp_unslash( $_POST['decimal_separator'] ) ) : '';
		$precision          = isset( $_POST['precision'] ) ? absint( wp_unslash( $_POST['precision'] ) ) : '';
		$position           = isset( $_POST['position'] ) ? sanitize_text_field( wp_unslash( $_POST['position'] ) ) : '';
		$status             = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$currency           = eac_insert_currency(
			array(
				'id'                 => $id,
				'code'               => $code,
				'name'               => $name,
				'symbol'             => $symbol,
				'exchange_rate'      => $exchange_rate,
				'thousand_separator' => $thousand_separator,
				'decimal_separator'  => $decimal_separator,
				'precision'          => $precision,
				'position'           => $position,
				'status'             => $status,
			)
		);

		if ( is_wp_error( $currency ) ) {
			EAC()->flash->error( $currency->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Currency saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $currency->id, $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Edit tax.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function handle_edit_tax() {
		check_admin_referer( 'eac_edit_tax' );
		$referer = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$rate        = isset( $_POST['rate'] ) ? doubleval( wp_unslash( $_POST['rate'] ) ) : '';
		$is_compound = isset( $_POST['is_compound'] ) ? sanitize_text_field( wp_unslash( $_POST['is_compound'] ) ) : '';
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		if( $is_compound ) {
			$is_compound = 'yes' === $is_compound ? true : false;
		}
		$tax         = eac_insert_tax(
			array(
				'id'          => $id,
				'name'        => $name,
				'rate'        => $rate,
				'is_compound' => $is_compound,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $tax ) ) {
			EAC()->flash->error( $tax->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Tax saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $tax->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
