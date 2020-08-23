<?php
/**
 * Admin Expense by Category Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Overview/Metaboxes
 * @package     EverAccounting
 */

namespace EverAccounting\Admin\Overview;

use EverAccounting\Abstracts\MetaBox;
use EverAccounting\Query_Transaction;

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
	 * $this->action        = 'eaccounting_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'wp-ever-accounting' );
	 *
	 * @access  public
	 * @since   1.0.2
	 * @return  void
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
	 * @since  1.0.2
	 * @return mixed content The metabox content.
	 */
	public function content() {

		?>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad, molestiae.
		<?php
	}
}
