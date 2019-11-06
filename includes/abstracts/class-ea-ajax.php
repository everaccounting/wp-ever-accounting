<?php

defined( 'ABSPATH' ) || exit();

abstract class EAccounting_Ajax{

	/**
	 * Add action
	 *
	 * @since 1.0.0
	 * @param $tag
	 * @param $callback
	 * @param bool $public
	 */
	public function action($tag, $callback, $public = false ){
		$function = sanitize_key(sprintf('wp_ajax_%s', $tag));
		add_action( $function, [ $this, $callback ] );
	}

	/**
	 * Send success data
	 * since 1.0.0
	 * @param $data
	 * @param string $code
	 */
	public function success($data, $code = ''){
		wp_send_json_success($data);
	}

	/**
	 * Send error data
	 * since 1.0.0
	 * @param $message
	 * @param string $code
	 */
	public function error($message, $code = ''){
		wp_send_json_error([
			'message' => $message,
		]);
	}
}
