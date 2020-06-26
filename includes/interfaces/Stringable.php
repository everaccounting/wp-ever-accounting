<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Stringable' ) ):
	interface Stringable {
		/**
		 * Returns object as string.
		 * @since 1.0.0
		 */
		public function __toString();
	}
endif;
