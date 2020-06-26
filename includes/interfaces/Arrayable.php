<?php
defined( 'ABSPATH' ) || exit();

if(!class_exists('Arrayable')):
interface Arrayable {
	/**
	 * Returns object as string.
	 * @since 1.0.0
	 */
	public function __toArray();

	/**
	 * Returns object as string.
	 * @since 1.0.0
	 */
	public function toArray();
}
endif;
