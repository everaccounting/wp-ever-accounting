<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;

/**
 * Class Categories.
 *
 * @since   1.0.2
 * @package EverAccounting\Admin\ListTables
 */
class Categories extends ListTable {
	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.2
	 */
	public function __construct( $args = array() ) {
		$args         = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'category',
				'plural'   => 'categories',
			)
		);
		$this->screen = get_current_screen();
		parent::__construct( $args );
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args              = array(
			'limit'   => $this->get_per_page(),
			'offset'  => $this->get_offset(),
			'status'  => $this->get_status(),
			'search'  => $this->get_search(),
			'order'   => $this->get_order( 'ASC' ),
			'orderby' => $this->get_orderby( 'status' ),
		);
		$this->items       = eac_get_categories( $args );
		$this->total_count = eac_get_categories( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $this->get_per_page(),
				'total_pages' => ceil( $this->total_count / $this->get_per_page() ),
			)
		);
	}

	/**
	 * No items found text.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function no_items() {
		esc_html_e( 'No categories found.', 'wp-ever-accounting' );
	}

	/**
	 * Adds the order and product filters to the licenses list.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 *
	 * @since 1.0.2
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		echo '<div class="alignleft actions">';
		$this->status_filter();
		submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter', false );
		echo '</div>';
		$filter = eac_get_input_var( INPUT_GET, 'filter' );
		if ( ! empty( $filter ) || ! empty( $this->get_search() ) ) {
			echo sprintf(
				'<a href="%s" class="button">%s</a>',
				esc_url( $this->get_current_url() ),
				esc_html__( 'Reset', 'wp-ever-accounting' )
			);
		}
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
	public function process_bulk_action( $doaction ) {
		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
			$id  = eac_get_input_var( 'category_id' );
			$ids = eac_get_input_var( 'category_ids' );
			if ( ! empty( $id ) ) {
				$ids      = wp_parse_id_list( $id );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			switch ( $doaction ) {
				case 'activate':
					$changed = 0;

					foreach ( $ids as $id ) {
						$args = array(
							'id'     => $id,
							'status' => 'active',
						);
						if ( eac_insert_category( $args ) ) {
							$changed ++;
						}
					}
					eac_add_notice( sprintf( __( '%s category(s) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $changed ) ), 'success' );

					break;

				case 'deactivate':
					$changed = 0;

					foreach ( $ids as $id ) {
						$args = array(
							'id'     => $id,
							'status' => 'inactive',
						);
						if ( eac_insert_category( $args ) ) {
							$changed ++;
						}
					}
					eac_add_notice( sprintf( __( '%s category(s) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $changed ) ), 'success' );

					break;

				case 'delete':
					$changed = 0;

					foreach ( $ids as $id ) {
						if ( eac_delete_category( $id ) ) {
							$changed ++;
						}
					}

					eac_add_notice( sprintf( __( '%s category(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $changed ) ), 'success' );

					break;

			}

			wp_safe_redirect( admin_url( 'admin.php?page=eac-settings&tab=categories' ) );
			exit();
		}

		parent::process_bulk_actions( $doaction );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'wp-ever-accounting' ),
			'description' => __( 'Description', 'wp-ever-accounting' ),
			'type'        => __( 'Type', 'wp-ever-accounting' ),
			'status'      => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function get_sortable_columns() {
		return array(
			'name'        => array( 'name', false ),
			'description' => array( 'description', false ),
			'type'        => array( 'type', false ),
			'status'      => array( 'status', false ),
		);
	}

	/**
	 * Get bulk actions
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
			'delete'     => __( 'Delete', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column in the accounts list table.
	 *
	 * @param Category $item The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="category_ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param Category $item The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 */
	public function column_name( $item ) {
		$args        = array( 'category_id' => $item->get_id() );
		$edit_url    = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$enable_url  = $this->get_current_url( array_merge( $args, array( 'action' => 'activate' ) ) );
		$disable_url = $this->get_current_url( array_merge( $args, array( 'action' => 'deactivate' ) ) );
		$delete_url  = $this->get_current_url( array_merge( $args, array( 'action' => 'delete' ) ) );
		$actions     = array(
			'id'      => sprintf( '<strong>#%d</strong>', esc_attr( $item->get_id() ) ),
			'edit'    => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'enable'  => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $enable_url, 'bulk-categories' ) ), __( 'Activate', 'wp-ever-accounting' ) ),
			'disable' => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $disable_url, 'bulk-categories' ) ), __( 'Deactivate', 'wp-ever-accounting' ) ),
			'delete'  => sprintf( '<a href="%s" class="del">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-categories' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( 'active' === $item->get_status() ) {
			unset( $actions['enable'] );
		} else {
			unset( $actions['disable'] );
		}

		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item->get_name() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Category $item The current account object.
	 * @param string   $column_name The name of the column.
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'type':
				$types = eac_get_category_types();
				$value = isset( $types[ $item->get_type() ] ) ? $types[ $item->get_type() ] : '&mdash;';
				break;
			case 'status':
				$value = esc_html( $item->get_status( 'view' ) );
				$value = $value ? sprintf( '<span class="eac-status-label %s">%s</span>', esc_attr( $item->get_status() ), $value ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
