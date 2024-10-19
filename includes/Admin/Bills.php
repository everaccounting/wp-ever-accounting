<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Bill;
use EverAccounting\Models\DocumentItem;
use EverAccounting\Models\DocumentTax;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bills
 *
 * @package EverAccounting\Admin\Sales
 */
class Bills {

	/**
	 * Bills constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_purchases_page_bills_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_purchases_page_bills_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_purchases_page_bills_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_bill_edit_side_meta_boxes', array( __CLASS__, 'bill_attachment' ) );
		add_action( 'eac_bill_view_side_meta_boxes', array( __CLASS__, 'bill_attachment' ) );
		add_action( 'eac_bill_edit_side_meta_boxes', array( __CLASS__, 'bill_notes' ) );
		add_action( 'eac_bill_view_side_meta_boxes', array( __CLASS__, 'bill_notes' ) );
		add_action( 'wp_ajax_eac_get_bill_address_html', array( __CLASS__, 'get_billing_address_html' ) );
		add_action( 'wp_ajax_eac_bill_recalculated_html', array( __CLASS__, 'bill_recalculated_html' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		if ( current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_edit_bill' === $_POST['action'] && check_admin_referer( 'eac_edit_bill' ) && current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$bill    = EAC()->bills->insert( $_POST );
			$referer = wp_get_referer();
			if ( is_wp_error( $bill ) ) {
				EAC()->flash->error( $bill->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Bill saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $bill->id, $referer );
				$referer = add_query_arg( 'action', 'view', $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}
		if ( isset( $_POST['action'] ) && 'eac_update_bill' === $_POST['action'] && check_admin_referer( 'eac_update_bill' ) && current_user_can( 'eac_manage_bill' ) ) {// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$id            = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
			$status        = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
			$bill_action   = isset( $_POST['bill_action'] ) ? sanitize_text_field( wp_unslash( $_POST['bill_action'] ) ) : '';

			$bill = EAC()->bills->get( $id );

			// If bill not found, bail.
			if ( ! $bill ) {
				wp_die( esc_html__( 'You attempted to update an bill that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
			}

			// Update bill status.
			if ( ! empty( $status ) && $status !== $bill->status ) {
				$bill->status = $status;
			}

			// Update bill attachment.
			if ( $attachment_id !== $bill->attachment_id ) {
				$bill->attachment_id = $attachment_id;
			}

			if ( $bill->is_dirty() && $bill->save() ) {
				$ret = $bill->save();
				if ( is_wp_error( $ret ) ) {
					EAC()->flash->error( $ret->get_error_message() );
				} else {
					EAC()->flash->success( __( 'Bill updated successfully.', 'wp-ever-accounting' ) );
				}
			}

			// todo handle payment action.
			if ( ! empty( $bill_action ) ) {
				switch ( $bill_action ) {
					case 'send_receipt':
						// Send bill.
						break;
					default:
						/**
						 * Fires action to handle custom bill actions.
						 *
						 * @param Bill $bill Payment object.
						 *
						 * @since 1.0.0
						 */
						do_action( 'eac_bill_action_' . $bill_action, $bill );
						break;
				}
			}
		}
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

			case 'view':
			case 'edit':
				$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
				if ( ! EAC()->bills->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve an bill that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Bills();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_bills_per_page',
					)
				);
				break;
		}
	}

	/**
	 * Handle page content.
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
				include __DIR__ . '/views/bill-edit.php';
				break;
			case 'view':
				include __DIR__ . '/views/bill-view.php';
				break;
			default:
				include __DIR__ . '/views/bill-list.php';
				break;
		}
	}

	/**
	 * Bill attachment.
	 *
	 * @param Bill $bill Bill object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function bill_attachment( $bill ) {
		?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<?php eac_file_uploader( array( 'value' => $bill->attachment_id ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Bill notes.
	 *
	 * @param Bill $bill Bill object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function bill_notes( $bill ) {
		// bail if bill not exist.
		if ( ! $bill->exists() ) {
			return;
		}

		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $bill->id,
				'parent_type' => 'bill',
				'orderby'     => 'date_created',
				'order'       => 'DESC',
				'limit'       => 20,
			)
		);
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<div class="eac-form-field">
					<label for="eac-note"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
					<textarea id="eac-note" cols="30" rows="2" placeholder="<?php esc_attr_e( 'Enter Note', 'wp-ever-accounting' ); ?>"></textarea>
				</div>
				<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $bill->id ); ?>" data-parent_type="bill" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
					<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
				</button>

				<?php include __DIR__ . '/views/note-list.php'; ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Get bill billings.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function get_billing_address_html() {
		check_ajax_referer( 'eac_edit_bill' );

		if ( ! current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$vendor_id = isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0;
		$vendor    = EAC()->vendors->get( $vendor_id );
		if ( ! $vendor ) {
			wp_die( - 1 );
		}
		$bill                     = new Bill();
		$bill->contact_id         = $vendor_id;
		$bill->contact_name       = $vendor->name;
		$bill->contact_email      = $vendor->email;
		$bill->contact_phone      = $vendor->phone;
		$bill->contact_address    = $vendor->address;
		$bill->contact_city       = $vendor->city;
		$bill->contact_state      = $vendor->state;
		$bill->contact_postcode   = $vendor->postcode;
		$bill->contact_country    = $vendor->country;
		$bill->contact_tax_number = $vendor->tax_number;

		ob_start();
		include __DIR__ . '/views/bill-editor-address.php';
		$html = ob_get_clean();

		$x = new \WP_Ajax_Response();
		$x->add(
			array(
				'what' => 'billings_html',
				'data' => $html,
			)
		);

		$x->send();

		wp_die( 1 );
	}

	/**
	 * Get recalculated html.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function bill_recalculated_html() {
		check_ajax_referer( 'eac_edit_bill' );

		if ( ! current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$id                   = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$items                = isset( $_POST['items'] ) ? map_deep( wp_unslash( $_POST['items'] ), 'sanitize_text_field' ) : array();
		$bill                 = Bill::make( $id );
		$bill->currency       = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency();
		$bill->exchange_rate  = isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1;
		$bill->discount_type  = isset( $_POST['discount_type'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_type'] ) ) : 'fixed';
		$bill->discount_value = isset( $_POST['discount_value'] ) ? floatval( wp_unslash( $_POST['discount_value'] ) ) : 0;
		$bill->items          = array();
		foreach ( $items as &$item_data ) {
			$item_data['quantity'] = isset( $item_data['quantity'] ) ? floatval( $item_data['quantity'] ) : 1;
			$item_data['item_id']  = isset( $item_data['item_id'] ) ? absint( $item_data['item_id'] ) : 0;
			$item                  = EAC()->items->get( $item_data['item_id'] );

			if ( ! $item || $item_data['quantity'] <= 0 ) {
				continue;
			}

			$bill_item              = DocumentItem::make();
			$bill_item->name        = isset( $item_data['name'] ) ? sanitize_text_field( $item_data['name'] ) : $item->name;
			$bill_item->description = isset( $item_data['description'] ) ? sanitize_text_field( $item_data['description'] ) : $item->description;
			$bill_item->unit        = isset( $item_data['unit'] ) ? sanitize_text_field( $item_data['unit'] ) : $item->unit;
			$bill_item->type        = isset( $item_data['type'] ) ? sanitize_text_field( $item_data['type'] ) : $item->type;
			$bill_item->price       = isset( $item_data['price'] ) ? floatval( $item_data['price'] ) : $item->cost;
			$bill_item->subtotal    = isset( $item_data['subtotal'] ) ? floatval( $item_data['subtotal'] ) : $bill_item->price * $bill_item->quantity;
			$bill_item->discount    = 0;
			$bill_item->tax         = 0;
			$bill_item->total       = 0;
			$bill_item->taxes       = array();
			$item_taxes             = isset( $item_data['taxes'] ) ? map_deep( $item_data['taxes'], 'sanitize_text_field' ) : array();
			foreach ( $item_taxes as &$tax_data ) {
				$tax_data['tax_id'] = isset( $tax_data['tax_id'] ) ? absint( $tax_data['tax_id'] ) : 0;
				$tax_rate           = EAC()->taxes->get( $tax_data['tax_id'] );

				if ( ! $tax_rate ) {
					continue;
				}

				$bill_tax         = DocumentTax::make();
				$bill_tax->tax_id = $tax_rate->id;
				$bill_tax->name   = isset( $tax_data['name'] ) ? sanitize_text_field( $tax_data['name'] ) : $tax_rate->name;
				$bill_tax->rate   = isset( $tax_data['rate'] ) ? floatval( $tax_data['rate'] ) : $tax_rate->rate;
				$bill_tax->amount = 0;
			}

			wp_send_json( $bill_item->to_array() );
		}

		wp_send_json( $bill->to_array() );
	}

	/**
	 * Get recalculated html.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function bill_recalculated_html_v1() {
		check_ajax_referer( 'eac_edit_bill' );

		if ( ! current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( - 1 );
		}

		$bill                 = Bill::make( isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0 );
		$bill->currency       = isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency();
		$bill->exchange_rate  = isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1;
		$bill->discount_type  = isset( $_POST['discount_type'] ) ? sanitize_text_field( wp_unslash( $_POST['discount_type'] ) ) : 'fixed';
		$bill->discount_value = isset( $_POST['discount_value'] ) ? floatval( wp_unslash( $_POST['discount_value'] ) ) : 0;

		if ( array_key_exists( 'items', $_POST ) && is_array( $_POST['items'] ) ) {
			$bill->items = array();
			$items       = map_deep( wp_unslash( $_POST['items'] ), 'sanitize_text_field' );
			foreach ( $items as $i => &$item_data ) {
				$item_data['quantity'] = isset( $item_data['quantity'] ) ? floatval( $item_data['quantity'] ) : 1;
				$item_data['item_id']  = isset( $item_data['item_id'] ) ? absint( $item_data['item_id'] ) : 0;
				$item                  = EAC()->items->get( $item_data['item_id'] );

				if ( ! $item || $item_data['quantity'] <= 0 ) {
					unset( $items[ $i ] );
					continue;
				}

				$item_data['id']          = 'new-' . uniqid();
				$item_data['name']        = isset( $item_data['name'] ) ? sanitize_text_field( $item_data['name'] ) : $item->name;
				$item_data['description'] = isset( $item_data['description'] ) ? sanitize_text_field( $item_data['description'] ) : $item->description;
				$item_data['unit']        = isset( $item_data['unit'] ) ? sanitize_text_field( $item_data['unit'] ) : $item->unit;
				$item_data['type']        = isset( $item_data['type'] ) ? sanitize_text_field( $item_data['type'] ) : $item->type;
				$item_data['price']       = isset( $item_data['price'] ) ? floatval( $item_data['price'] ) : $item->price;
				$item_data['subtotal']    = isset( $item_data['subtotal'] ) ? floatval( $item_data['subtotal'] ) : $item_data['price'] * $item_data['quantity'];
				$item_data['discount']    = 0;
				$item_data['tax']         = 0;
				$item_data['total']       = 0;
				if ( array_key_exists( 'taxes', $item_data ) && is_array( $item_data['taxes'] ) ) {
					foreach ( $item_data['taxes'] as $j => &$tax_data ) {
						$tax_data['tax_id'] = isset( $tax_data['tax_id'] ) ? absint( $tax_data['tax_id'] ) : 0;
						$tax_rate           = EAC()->taxes->get( $tax_data['tax_id'] );

						if ( ! $tax_rate ) {
							unset( $item_data['taxes'][ $j ] );
							continue;
						}

						$tax_data['id']           = 'new-' . uniqid();
						$tax_data['name']         = isset( $tax_data['name'] ) ? sanitize_text_field( $tax_data['name'] ) : $tax_rate->name;
						$tax_data['rate']         = isset( $tax_data['rate'] ) ? floatval( $tax_data['rate'] ) : $tax_rate->rate;
						$tax_data['amount']       = 0;
						$item_data['taxes'][ $j ] = $tax_data;
					}
				}
			}

			$items_total = array_sum( wp_list_pluck( wp_list_filter( $items, array( 'type' => 'fee' ), 'NOT' ), 'subtotal' ) );
			$discount    = 'fixed' === $discount_type ? $discount_value : ( $items_total * $discount_value / 100 );
			$discount    = max( $items_total, $discount );

			foreach ( $items as $i => &$item_data ) {
				$_type       = ! empty( $item_data['type'] ) ? $item_data['type'] : 'standard';
				$_subtotal   = empty( $item_data['subtotal'] ) ? 0 : $item_data['subtotal'];
				$_discount   = 'standard' === $_type ? ( $discount / $items_total ) * $_subtotal : 0;
				$_discounted = max( $_subtotal - $_discount, 0 );

				// Simple tax calculation.
				foreach ( $item_data['taxes'] as $j => &$tax_data ) {
					$tax_data['amount'] = empty( $tax_data['compound'] ) ? ( $_discounted * $tax_data['rate'] / 100 ) : 0;
				}

				$pre_compound_tax = array_sum( wp_list_pluck( $item_data['taxes'], 'amount' ) );
				foreach ( $item_data['taxes'] as $j => &$tax_data ) {
					$tax_data['amount'] = empty( $tax_data['compound'] ) ? $tax_data['amount'] : ( $pre_compound_tax + $_discounted ) * $tax_data['rate'] / 100;
				}
			}
		}
	}

	/**
	 * Get sanitized bill data.
	 *
	 * @param array $posted Posted data.
	 *
	 * @since 1.2.0
	 * @return array
	 */
	public function get_sanitized_bill_data( $posted ) {
		$posted['contact_id']      = isset( $posted['contact_id'] ) ? absint( $posted['contact_id'] ) : 0;
		$posted['contact_name']    = isset( $posted['contact_name'] ) ? sanitize_text_field( $posted['contact_name'] ) : '';
		$posted['contact_email']   = isset( $posted['contact_email'] ) ? sanitize_email( $posted['contact_email'] ) : '';
		$posted['contact_company'] = isset( $posted['contact_company'] ) ? sanitize_text_field( $posted['contact_company'] ) : '';
		$posted['contact_phone']   = isset( $posted['contact_phone'] ) ? sanitize_text_field( $posted['contact_phone'] ) : '';
		$posted['contact_address'] = isset( $posted['contact_address'] ) ? sanitize_text_field( $posted['contact_address'] ) : '';
		$posted['contact_city']    = isset( $posted['contact_city'] ) ? sanitize_text_field( $posted['contact_city'] ) : '';
		$contact_state             = isset( $posted['contact_state'] ) ? sanitize_text_field( $posted['contact_state'] ) : '';
		$contact_postcode          = isset( $posted['contact_postcode'] ) ? sanitize_text_field( $posted['contact_postcode'] ) : '';
		$contact_country           = isset( $posted['contact_country'] ) ? sanitize_text_field( $posted['contact_country'] ) : '';
		$contact_tax_number        = isset( $posted['contact_tax_number'] ) ? sanitize_text_field( $posted['contact_tax_number'] ) : '';
		$reference                 = isset( $posted['reference'] ) ? sanitize_text_field( $posted['reference'] ) : '';
		$issue_date                = isset( $posted['issue_date'] ) ? sanitize_text_field( $posted['issue_date'] ) : '';
		$due_date                  = isset( $posted['due_date'] ) ? sanitize_text_field( $posted['due_date'] ) : '';
		$currency_code             = isset( $posted['currency_code'] ) ? sanitize_text_field( $posted['currency_code'] ) : '';
		$exchange_rate             = isset( $posted['exchange_rate'] ) ? floatval( $posted['exchange_rate'] ) : 1;
		$items                     = isset( $posted['items'] ) ? $posted['items'] : array();
	}
}
