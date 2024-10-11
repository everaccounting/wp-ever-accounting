<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Class Items
 *
 * @since 2.0.0
 * @package EverAccounting\Admin\Items
 */
class Items {

	/**
	 * Items constructor.
	 */
	public function __construct() {
		add_filter( 'eac_items_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_items_page_items_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'admin_post_eac_edit_item', array( __CLASS__, 'handle_edit' ) );
		add_action( 'eac_items_page_items_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_item_edit_core_meta_boxes', array( __CLASS__, 'data_meta_box' ) );
		add_action( 'eac_item_edit_side_meta_boxes', array( __CLASS__, 'actions_meta_box' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		if ( current_user_can( 'eac_manage_item' ) ) {
			$tabs['items'] = __( 'Items', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle page loaded.
	 *
	 * @param string $action Current action.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function page_loaded( $action ) {
		global $list_table;
		switch ( $action ) {
			case 'add':
				// Nothing to do here.
				break;

			case 'edit':
				$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
				if ( ! EAC()->items->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to edit an item that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Items();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_items_per_page',
					)
				);
				break;
		}
	}

	/**
	 * Save item.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_item' );
		if ( ! current_user_can( 'eac_manage_item' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( esc_html__( 'You are not allowed to perform this action.', 'wp-ever-accounting' ) );
		}
		$referer = wp_get_referer();
		$data    = array();
		if ( array_key_exists( 'id', $_POST ) ) {
			$data['id'] = absint( wp_unslash( $_POST['id'] ) );
		}
		if ( array_key_exists( 'name', $_POST ) ) {
			$data['name'] = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		}
		if ( array_key_exists( 'type', $_POST ) ) {
			$data['type'] = sanitize_text_field( wp_unslash( $_POST['type'] ) );
		}
		if ( array_key_exists( 'price', $_POST ) ) {
			$data['price'] = floatval( wp_unslash( $_POST['price'] ) );
		}
		if ( array_key_exists( 'cost', $_POST ) ) {
			$data['cost'] = floatval( wp_unslash( $_POST['cost'] ) );
		}
		if ( array_key_exists( 'category_id', $_POST ) ) {
			$data['category_id'] = absint( wp_unslash( $_POST['category_id'] ) );
		}
		if ( array_key_exists( 'unit', $_POST ) ) {
			$data['unit'] = sanitize_text_field( wp_unslash( $_POST['unit'] ) );
		}
		if ( array_key_exists( 'tax_ids', $_POST ) ) {
			$data['tax_ids'] = array_map( 'absint', wp_unslash( $_POST['tax_ids'] ) );
		}
		if ( array_key_exists( 'description', $_POST ) ) {
			$data['description'] = sanitize_textarea_field( wp_unslash( $_POST['description'] ) );
		}

		$item = EAC()->items->insert( $data );

		if ( is_wp_error( $item ) ) {
			EAC()->flash->error( $item->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Item saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg(
				array(
					'action' => 'edit',
					'id'     => $item->id,
				),
				$referer
			);
		}

		wp_safe_redirect( $referer );
		exit();
	}

	/**
	 * Render page content.
	 *
	 * @param string $action Current action.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function page_content( $action ) {
		switch ( $action ) {
			case 'add':
			case 'edit':
				include __DIR__ . '/views/item-edit.php';
				break;
			default:
				include __DIR__ . '/views/item-list.php';
				break;
		}
	}

	/**
	 * Data meta box.
	 *
	 * @param Item $item Item object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function data_meta_box( $item ) {
		?>
		<div id="eac-item-data" class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Item Data', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body grid--fields">
				<?php
				eac_form_field(
					array(
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'type'        => 'text',
						'name'        => 'name',
						'value'       => $item->name,
						'placeholder' => __( 'Laptop', 'wp-ever-accounting' ),
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'type'     => 'select',
						'name'     => 'type',
						'required' => true,
						'default'  => 'product',
						'label'    => __( 'Type', 'wp-ever-accounting' ),
						'value'    => $item->type,
						'options'  => EAC()->items->get_types(),
						'tooltip'  => __( 'Select the item type: Standard for regular products eligible for discounts, or Fee for extra charges that do not support discounts.', 'wp-ever-accounting' ),
					)
				);
				eac_form_field(
					array(
						'type'          => 'text',
						'name'          => 'price',
						'label'         => __( 'Price', 'wp-ever-accounting' ),
						'value'         => $item->price,
						'placeholder'   => __( '10.00', 'wp-ever-accounting' ),
						'required'      => true,
						/* translators: %s: currency symbol */
						'tooltip'       => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_base_currency() ),
						'class'         => 'eac_amount',
						'data-currency' => eac_base_currency(),
					)
				);
				eac_form_field(
					array(
						'type'          => 'text',
						'name'          => 'cost',
						'label'         => __( 'Cost', 'wp-ever-accounting' ),
						'value'         => $item->cost,
						'placeholder'   => __( '8.00', 'wp-ever-accounting' ),
						/* translators: %s: currency symbol */
						'tooltip'       => sprintf( __( 'Enter the cost of the item in %s.', 'wp-ever-accounting' ), eac_base_currency() ),
						'class'         => 'eac_amount',
						'data-currency' => eac_base_currency(),
					)
				);
				eac_form_field(
					array(
						'type'             => 'select',
						'name'             => 'category_id',
						'label'            => __( 'Category', 'wp-ever-accounting' ),
						'value'            => $item->category_id,
						'options'          => array( $item->category ),
						'option_label'     => 'formatted_name',
						'option_value'     => 'id',
						'data-placeholder' => __( 'Select item category', 'wp-ever-accounting' ),
						'class'            => 'eac_select2',
						'data-action'      => 'eac_json_search',
						'data-type'        => 'category',
						'data-subtype'     => 'item',
						'suffix'           => sprintf(
							'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( 'admin.php?page=eac-misc&tab=categories&add=yes' ),
							__( 'Add Category', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'unit',
						'label'       => __( 'Unit', 'wp-ever-accounting' ),
						'value'       => $item->unit,
						'options'     => EAC()->items->get_units(),
						'placeholder' => __( 'Select unit', 'wp-ever-accounting' ),
						'class'       => 'eac-select2',
					)
				);
				// tax_ids.
				eac_form_field(
					array(
						'type'         => 'select',
						'multiple'     => true,
						'name'         => 'tax_ids',
						'label'        => __( 'Taxes', 'wp-ever-accounting' ),
						'value'        => $item->tax_ids,
						'options'      => $item->taxes,
						'option_label' => 'formatted_name',
						'option_value' => 'id',
						'class'        => 'eac_select2',
						'data-action'  => 'eac_json_search',
						'data-type'    => 'tax',
						'tooltip'      => __( 'The selected tax rates will be applied to this item.', 'wp-ever-accounting' ),
					)
				);

				eac_form_field(
					array(
						'type'          => 'textarea',
						'name'          => 'description',
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'value'         => $item->description,
						'wrapper_class' => 'is--full',
					)
				);
				?>
			</div>
		</div><!-- .eac-card -->
		<?php
	}

	/**
	 * Actions meta box.
	 *
	 * @param Item $item Item object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function actions_meta_box( $item ) {
		?>
		<div id="eac-item-actions" class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
			</div>
			<?php if ( has_action( 'eac_item_misc_actions' ) ) : ?>
				<div class="eac-card__body">
					<?php
					/**
					 * Fires when item-specific meta boxes are added.
					 *
					 * @param Item $item Item object.
					 *
					 * @since 2.0.0
					 */
					do_action( 'eac_item_misc_actions', $item );
					?>
				</div>
			<?php endif; ?>
			<div class="eac-card__footer">
				<?php if ( $item->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-items&id=' . $item->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Update Item', 'wp-ever-accounting' ); ?></button>
				<?php else : ?>
					<button class="button button-primary tw-w-[100%]"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
				<?php endif; ?>
			</div>
		</div><!-- .eac-card -->
		<?php
	}
}
