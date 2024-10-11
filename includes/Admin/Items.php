<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Class Items
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Items
 */
class Items {

	/**
	 * Items constructor.
	 */
	public function __construct() {
		add_filter( 'eac_items_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_items_page_items', array( __CLASS__, 'setup_page' ) );
		add_action( 'admin_post_eac_edit_item', array( __CLASS__, 'save_item' ) );
		add_action( 'eac_items_page_items', array( __CLASS__, 'render_page' ) );
		add_action( 'eac_item_add_primary', array( __CLASS__, 'item_attributes' ) );
		add_action( 'eac_item_edit_primary', array( __CLASS__, 'item_attributes' ) );
		add_action( 'eac_item_add_secondary', array( __CLASS__, 'item_actions' ) );
		add_action( 'eac_item_edit_secondary', array( __CLASS__, 'item_actions' ) );
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
		if ( current_user_can( 'eac_manage_item' ) ) {
			$tabs['items'] = __( 'Items', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * setup page.
	 *
	 * @param string $action Current action.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_page( $action ) {
		global $list_table;
		if ( ! in_array( $action, array( 'new', 'edit' ), true ) ) {
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
		}
	}

	/**
	 * Handle edit.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function save_item() {
		check_admin_referer( 'eac_edit_item' );
		$referer = wp_get_referer();
		$item    = EAC()->items->insert( $_POST );

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
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit();
	}

	/**
	 * Render page.
	 *
	 * @param string $action View.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_page( $action ) {
		switch ( $action ) {
			case 'add':
				include __DIR__ . '/views/item-add.php';
				break;
			case 'edit':
				include __DIR__ . '/views/item-edit.php';
				break;
			default:
				include __DIR__ . '/views/item-list.php';
				break;
		}
	}

	/**
	 * Render attributes fields.
	 *
	 * @param Item $item Item.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function item_attributes( $item ) {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Item Attributes', 'wp-ever-accounting' ); ?></h3>
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
	 * Render item actions.
	 *
	 * @param Item $item Item.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function item_actions( $item ) {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__footer">
				<?php if ( $item->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-items&id=' . $item->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Update Item', 'wp-ever-accounting' ); ?></button>
				<?php else : ?>
					<button class="button button-primary eac-width-full"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
