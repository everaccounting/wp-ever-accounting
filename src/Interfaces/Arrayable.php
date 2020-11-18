<?php

namespace EverAccounting\Interfaces;

interface Arrayable {
	/**
	 * Returns object as string.
	 *
	 * @since 1.0.0
	 */
	public function __toArray();
	/**
	 * Returns object as string.
	 *
	 * @since 1.0.0
	 */
	public function to_array();
}
