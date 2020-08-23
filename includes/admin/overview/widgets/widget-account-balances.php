<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Query_Transaction;

class Account_Balances extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-account-balances';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-4';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Account Balances', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {

	}
}
