<?php

namespace EverAccounting\Contacts;

defined( 'ABSPATH' ) || exit();

class Query extends \EverAccounting\Query {
	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	const TABLE = 'ea_contacts';

	/**
	 * Table name in database (without prefix).
	 *
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'contacts';

	/**
	 * Get the default allowed query vars.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	protected function get_query_vars() {
		return wp_parse_args(
			array(
				'table'          => self::TABLE,
				'order'          => 'DESC',
				'search_columns' => array( 'name', 'email', 'phone', 'fax', 'address' ),
			),
			parent::get_query_vars()
		);
	}

	/**
	 * Parse extra args.
	 *
	 * @since 1.1.0
	 *
	 * @param $vars
	 */
	protected function parse_extra( $vars ) {
		if ( ! empty( $vars['type'] ) && array_key_exists( $vars['type'], get_types() ) ) {
			$this->where( 'type', $vars['type'] );
		}
		if ( isset( $vars['enabled'] ) && '' != trim($vars['enabled'])) { //@codingStandardsIgnoreLine
			$this->where( 'enabled', eaccounting_bool_to_number( $vars['enabled'] ) );
		}
		parent::get_query_vars( $vars );
	}

}
