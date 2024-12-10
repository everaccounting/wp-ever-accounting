<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomies class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Taxonomies {

	/**
	 * Taxonomies constructor.
	 */
	public function __construct() {
		add_filter( 'eac_settings_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'admin_post_eac_edit_taxonomy', array( __CLASS__, 'handle_edit' ) );

		add_action( 'eac_settings_page_taxonomies_content', array( __CLASS__, 'render_sections' ) );
		add_action( 'eac_settings_page_taxonomies_content', array( __CLASS__, 'render_content' ) );

		// Item Taxonomy.
		add_action( 'eac_settings_taxonomies_tab_item_content', array( __CLASS__, 'render_item_content' ) );
		add_action( 'eac_settings_taxonomies_tab_payment_content', array( __CLASS__, 'render_item_content' ) );
		add_action( 'eac_settings_taxonomies_tab_expense_content', array( __CLASS__, 'render_item_content' ) );

		// Filter the query arguments for the list table.
		add_filter( 'eac_taxonomies_table_query_args', array( $this, 'query_args' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['taxonomies'] = __( 'Taxonomies', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Edit taxonomy.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_taxonomy' );
		$referer = wp_get_referer();
		$data    = array(
			'id'          => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
			'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'type'        => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
			'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
		);

		$item = EAC()->categories->insert( $data );
		if ( is_wp_error( $item ) ) {
			EAC()->flash->error( $item->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'id', $item->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

	/**
	 * Handle actions.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_edit_category' === $_POST['action'] && check_admin_referer( 'eac_edit_category' ) && current_user_can( 'eac_manage_category' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$referer = wp_get_referer();
			$data    = array(
				'id'          => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'type'        => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
				'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
			);

			$item = EAC()->categories->insert( $data );
			if ( is_wp_error( $item ) ) {
				EAC()->flash->error( $item->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $item->id, $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}
	}
	/**
	 * Render Sections.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render_sections() {
		$section = (string) filter_input( INPUT_GET, 'section', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		$sections = self::get_sections();

		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}

		$array_keys = array_keys( $sections );
		echo '<ul class="subsubsub settings-sections-nav" style="margin-bottom: 1em;">';
		foreach ( $sections as $id => $label ) {
			$url   = admin_url( 'admin.php?page=eac-settings&tab=taxonomies&section=' . sanitize_title( $id ) );
			$class = ( $section === $id ? 'current' : '' );
			if ( empty( $section ) && 'item' === $id ) {
				$class = 'current';
			}
			$separator = ( end( $array_keys ) === $id ? '' : '|' );
			$text      = esc_html( $label );
			printf( '<li><a href="%s" class="%s">%s</a> %s</li>', esc_url( $url ), esc_attr( $class ), esc_html( $text ), esc_html( $separator ) );
		}
		echo '</ul><br class="clear" />';
	}

	/**
	 * Render content.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_content() {
		$section = (string) filter_input( INPUT_GET, 'section', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $section ) && in_array( 'item', array_keys( self::get_sections() ), true ) ) {
			$section = 'item';
		}
		$action = 'eac_settings_taxonomies_tab' . ( $section ? '_' . $section : '' ) . '_content';

		if ( has_action( $action ) ) {
			/**
			 * Fire action for the current tab.
			 *
			 * @param string $current_section Current section.
			 *
			 * @since 1.0.0
			 */
			do_action( $action, $section );
		}
	}

	/**
	 * Get settings tab sections.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function get_sections() {
		$sections = array(
			'item'    => __( 'Item', 'wp-ever-accounting' ),
			'payment' => __( 'Payment', 'wp-ever-accounting' ),
			'expense' => __( 'Expense', 'wp-ever-accounting' ),
		);

		/**
		 * Filters the sections for this settings page.
		 *
		 * @since 2.0.0
		 * @param array $sections The sections for this settings page.
		 */
		return (array) apply_filters( 'eac_get_settings_taxonomies_sections', $sections );
	}

	/**
	 * Render item content.
	 *
	 * @param string $section Current section.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_item_content( $section ) {
		$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$id     = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $section ) ) {
			$section = 'item';
		}

		switch ( $action ) {
			case 'add':
			case 'edit':
				include __DIR__ . '/views/taxonomy-edit.php';
				break;
			default:
				global $list_table;
				$list_table = new ListTables\Taxonomies();
				$list_table->prepare_items();
				include __DIR__ . '/views/taxonomy-list.php';
				break;
		}
	}

	/**
	 * Query args.
	 *
	 * @param array $args Query args.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function query_args( $args ) {
		$args['type'] = (string) filter_input( INPUT_GET, 'section', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( empty( $args['type'] ) && in_array( 'item', array_keys( self::get_sections() ), true ) ) {
			$args['type'] = 'item';
		}

		return $args;
	}
}
