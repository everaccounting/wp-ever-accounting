<?php
/**
 * List tables.
 *
 * @package  EverAccounting/Admin
 * @version  1.0.2
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

abstract class List_Table extends \WP_List_Table {
	/**
	 * Type of the table should be use plural name.
	 *
	 * This will be used for filtering methods.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected $list_table_type;

	/**
	 * Optional arguments to pass when preparing items.
	 *
	 * @since  1.0.2
	 * @var    array
	 */
	public $query_args = array();

	/**
	 * Optional arguments to pass when preparing items for display.
	 *
	 * @since  1.0.2
	 * @var    array
	 */
	public $display_args = array();

	/**
	 * Current screen object.
	 *
	 * @since  1.0.2
	 * @var    \WP_Screen
	 */
	public $screen;

	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.0.2
	 */
	public $per_page = 20;

	/**
	 * Sets up the list table instance.
	 *
	 * @access public
	 *
	 * @param array $args {
	 *     Optional. Arbitrary display and query arguments to pass through to the list table.
	 *     Default empty array.
	 *
	 * @type string $singular Singular version of the list table item.
	 * @type string $plural Plural version of the list table item.
	 * @type array $query_args Optional. Arguments to pass through to the query used for preparing items.
	 *                               Accepts any valid arguments accepted by the given query methods.
	 * @type array $display_args {
	 *         Optional. Arguments to pass through for use when displaying queried items.
	 *
	 * @type string $pre_table_callback Callback to fire at the top of the list table, just before the list
	 *                                            table navigation is displayed. Default empty (disabled).
	 * @type bool $hide_table_nav Whether to hide the entire table navigation at the top and bottom
	 *                                            of the list table. Will hide the bulk actions, extra tablenav, and
	 *                                            pagination. Use `$hide_bulk_options`, or `$hide_pagination` for more
	 *                                            fine-grained control. Default false.
	 * @type bool $hide_bulk_options Whether to hide the bulk options controls at the top and bottom of
	 *                                            the list table. Default false.
	 * @type array $hide_pagination Whether to hide the pagination controls at the top and bottom of the
	 *                                            list table. Default false.
	 * @type bool $columns_to_hide An array of column IDs to hide for the current instance of the list
	 *                                            table. Note: other columns may be already hidden depending on current
	 *                                            user settings determined by screen options column controls. Default
	 *                                            empty array.
	 * @type bool $hide_column_controls Whether to hide the screen options column controls for the list table.
	 *                                            This should always be enabled when instantiating a standalone list
	 *                                            table in sub-views such as view_affiliate or view_payout due to
	 *                                            conflicts introduced in column controls generated for list tables
	 *                                            instantiated at the primary-view level. Default false.
	 *     }
	 * }
	 * @see WP_List_Table::__construct()
	 *
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
				'display_args' => array(),
				'query_args'   => array(),
		) );

		$this->screen = get_current_screen();

		$this->display_args = wp_parse_args( $args['display_args'], array(
				'hide_table_nav'       => false,
				'hide_bulk_options'    => false,
				'hide_pagination'      => false,
				'columns_to_hide'      => array(),
				'hide_column_controls' => false,
		) );

		if ( ! empty( $args['query_args'] ) ) {
			$this->query_args = $args['query_args'];
			unset( $args['query_args'] );
		}

		unset( $args['display_args'] );

		$args = (array) wp_parse_args( $args, array(
				'ajax' => false,
		) );

		parent::__construct( $args );

		if ( empty( $this->list_table_type ) ) {
			die( 'property $list_table_type must be overridden in a subclass.' );
		}

	}

	/**
	 * Show blank slate.
	 *
	 * @param string $which String which tablenav is being shown.
	 */
	public function maybe_render_blank_state( $which ) {
		if ( 'bottom' === $which && $this->is_empty() ) {

			$this->render_blank_state();

			echo '<style type="text/css">.wp-list-table, .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub  { display: none; } .tablenav.bottom { height: auto; } </style>';
		}
	}

	/**
	 * Check if there is contents in the database.
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	protected function is_empty() {
		return false;
	}

	/**
	 * Render blank state. Extend to add content.
	 */
	protected function render_blank_state() {

	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0
	 */
	public function get_columns() {
		$columns = $this->define_columns();

		return apply_filters( 'eaccounting_' . $this->list_table_type . '_table_columns', $columns, $this );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function define_columns() {
		return array();
	}

	/**
	 * Define which columns are sortable
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_sortable_columns() {
		$columns = $this->define_sortable_columns();

		return apply_filters( 'eaccounting_' . $this->list_table_type . '_table_sortable_columns', $columns, $this );
	}

	/**
	 * Define sortable columns.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function define_sortable_columns() {
		return array();
	}

	/**
	 * Define which columns are hidden.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_hidden_columns() {
		$columns = $this->define_hidden_columns();

		return apply_filters( 'eaccounting_' . $this->list_table_type . '_table_hidden_columns', $columns, $this );
	}

	/**
	 * Define hidden columns.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function define_hidden_columns() {
		return array();
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column() {
		return '';
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 * @since 1.0.2
	 *
	 */
	public function get_bulk_actions() {
		$columns = $this->define_bulk_actions();

		return apply_filters( 'eaccounting_' . $this->list_table_type . '_table_bulk_actions', $columns, $this );
	}

	/**
	 * Define bulk actions
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function define_bulk_actions() {
		return array();
	}

	/**
	 * Retrieves a list of all, hidden,sortable, and primary columns, with filters applied.
	 *
	 * Also sets up column show/hide controls.
	 *
	 * @access protected
	 * @return array Column headers.
	 * @since  1.0.2
	 *
	 */
	protected function get_column_info() {
		if ( true === $this->display_args['hide_column_controls'] ) {
			$columns = $this->get_columns();

			$hidden = array();

			$sortable = $this->get_sortable_columns();

			return $this->_column_headers = array( $columns, $hidden, $sortable, $this->get_primary_column_name() );
		}

		return $this->_column_headers = parent::get_column_info();
	}

	/**
	 * Show the search field
	 *
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 * @since 1.0.2
	 *
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Generates the table navigation above or below the table.
	 *
	 * @access protected
	 *
	 * @param string $which Which location the bulk actions are being rendered for.
	 *  Will be 'top' or 'bottom'.
	 *
	 * @since  1.0.2
	 *
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}

		if ( ! empty( $this->display_args['pre_table_callback'] )
			 && is_callable( $this->display_args['pre_table_callback'] )
			 && 'top' === $which
		) {

			echo call_user_func( $this->display_args['pre_table_callback'] );
		}

		$this->maybe_render_blank_state( $which );

		if ( true !== $this->display_args['hide_table_nav'] ) : ?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>">

				<?php if ( $this->has_items() && true !== $this->display_args['hide_bulk_options'] ) : ?>
					<div class="alignleft actions bulkactions">
						<?php $this->bulk_actions( $which ); ?>
					</div>
				<?php endif;
				$this->extra_tablenav( $which );

				if ( true !== $this->display_args['hide_pagination'] ) :
					$this->pagination( $which );
				endif;
				?>

				<br class="clear"/>
			</div>
		<?php endif;
	}

	/**
	 * Generates the required HTML for a list of row action links.
	 *
	 * @param array[] $actions An array of action links.
	 * @param bool $always_visible Whether the actions should be always visible.
	 *
	 * @return string The HTML for the row actions.
	 * @since 1.0.2
	 *
	 */
	protected function row_actions( $actions, $always_visible = true ) {
		$action_count = count( $actions );
		if ( ! $action_count ) {
			return '';
		}

		$out = '<div class="ea-dropdown"><a href="#" role="button" data-toggle="dropdown" class="ea-dropdown-button"><i class="ea-icon-dropdown"></i></a><ul class="ea-dropdown-menu">';

		foreach ( $actions as $action => $args ) {
			$args       = wp_parse_args( $args, array(
					'base_uri'   => '',
					'query_args' => '',
					'nonce'      => '',
					'label'      => ''
			) );
			$base_uri   = empty( $args['base_uri'] ) ? false : $args['base_uri'];
			$query_args = empty( $args['query_args'] ) ? array() : $args['query_args'];
			$nonce      = empty( $args['nonce'] ) ? false : $args['nonce'];
			$label      = empty( $args['label'] ) ? '' : $args['label'];
			$query_args = array_merge( $query_args, array( 'action' => $action ) );

			if ( ! $nonce ) {
				$url = esc_url( add_query_arg( $query_args, $base_uri ) );
			} else {
				$url = wp_nonce_url( add_query_arg( $query_args, $base_uri ), $nonce );
			}

			$out .= sprintf( '<li><a href="%s" class="row-action %s">%s</a>', $url, $action, $label );
		}

		$out .= '</ul></div>';

		return $out;
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 *
	 * @param Object $item
	 * @param string $column_name The name of the column
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {

			default:
				$value = isset( $item->$column_name ) ? $item->$column_name : '';
				break;
		}

		return apply_filters( 'eaccounting_' . $this->list_table_type . '_table_' . $column_name, $value, $item );
	}

	/**
	 * Get number of items to display on a single page
	 *
	 *
	 * @return int
	 * @since 1.0.2
	 *
	 */
	protected function get_per_page() {
		$per_page = (int) apply_filters( 'eaccounting_' . $this->list_table_type . '_per_page', $this->per_page );

		return parent::get_items_per_page( 'eaccounting_' . $this->list_table_type . '_per_page', $per_page );
	}
}
