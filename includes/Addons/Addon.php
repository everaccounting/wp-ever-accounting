<?php

namespace EverAccounting\Addons;

use EverAccounting\Licensing\License;

defined( 'ABSPATH' ) || exit();

/**
 * Addon class.
 *
 * @since 1.0.0
 * @package EverAccounting
 *
 * @property License $license The license service.
 */
abstract class Addon extends \EverAccounting\ByteKit\Plugin {

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		parent::__construct( $data );
		$this->services->add( 'license', new License( $this ) );
	}
}
