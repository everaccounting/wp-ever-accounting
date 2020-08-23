<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;


class Cash_Flow extends Widget {
	/**
	 * Widget id.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-cashflow';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-12';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Cash Flow', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {

	}
}
