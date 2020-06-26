<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'JSONable' ) ):
	interface JSONable {
		/**
		 * Returns object as JSON string.
		 * @since 1.0.2
		 */
		public function __toJSON( $options = 0, $depth = 512 );

		/**
		 * Returns object as JSON string.
		 * @since 1.0.2
		 */
		public function toJSON( $options = 0, $depth = 512 );
	}
endif;
