<?php

defined( 'ABSPATH' ) || exit();

abstract class EAccounting_Ajax {

	/**
	 * Add action
	 *
	 * @param $tag
	 * @param $callback
	 * @param bool $public
	 *
	 * @since 1.0.0
	 */
	public function action( $tag, $callback, $public = false ) {
		$function = sanitize_key( sprintf( 'wp_ajax_%s', $tag ) );
		add_action( $function, [ $this, $callback ] );
	}

	/**
	 * Send success data
	 * since 1.0.0
	 *
	 * @param string $message
	 * @param array $data
	 * @param string $code
	 */
	public function success( $message, $redirect= '', $data = array(), $code = '' ) {
		wp_send_json_success( array_merge( $data, array( 'message' => $message, 'redirect' => $redirect ) ) );
	}

	/**
	 * Send error data
	 * since 1.0.0
	 *
	 * @param $message
	 * @param string $code
	 */
	public function error( $message, $code = '' ) {
		wp_send_json_error( [
			'message' => $message,
		] );
	}
}
