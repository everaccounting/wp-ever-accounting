<?php

namespace EverAccounting\Admin\Overview;

use EverAccounting\Abstracts\MetaBox;

defined( 'ABSPATH' ) || exit();

class Expense_By_Category extends MetaBox {
	/**
	 * Initialize.
	 *
	 * Define the meta box name, meta box id,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 * $this->action        = 'affwp_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		$this->action        = 'eaccounting_overview_meta_boxes';
		$this->meta_box_name = __( 'Metabox Title', 'wp-ever-accounting' );
		$this->meta_box_id   = 'overview-totals';
		$this->context       = 'primary';
	}

	/**
	 * Displays the content of the metabox.
	 *
	 * @return mixed content The metabox content.
	 * @since  1.9
	 */
	public function content() {
		?>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad, molestiae.
		<?php
	}
}
