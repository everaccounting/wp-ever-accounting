<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @package EverAccounting\Admin\Sales
 */
class Customers {

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_sales_page_customers_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_sales_page_customers_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_sales_page_customers_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_customer_view_section_overview', array( __CLASS__, 'overview_section' ) );
		add_action( 'eac_customer_view_section_payments', array( __CLASS__, 'payments_section' ) );
		add_action( 'eac_customer_view_section_invoices', array( __CLASS__, 'invoices_section' ) );
		add_action( 'eac_customer_view_section_notes', array( __CLASS__, 'notes_section' ) );
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
		if ( current_user_can( 'eac_manage_customer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle actions.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_add_customer_note' === $_POST['action'] && check_admin_referer( 'eac_add_customer_note' ) && current_user_can( 'eac_manage_customer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$data = array(
				'content'     => isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '',
				'parent_id'   => isset( $_POST['parent_id'] ) ? absint( $_POST['parent_id'] ) : 0,
				'parent_type' => 'customer',
				'creator_id'  => get_current_user_id(),
				'created_at'  => current_time( 'mysql' ),
			);

			if ( empty( $data['content'] ) ) {
				EAC()->flash->error( __( 'Note content is required.', 'wp-ever-accounting' ) );

				return;
			}

			$note = EAC()->notes->insert( $data );

			if ( is_wp_error( $note ) ) {
				EAC()->flash->error( $note->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Note added successfully.', 'wp-ever-accounting' ) );
			}
		} elseif ( isset( $_POST['action'] ) && 'eac_edit_customer' === $_POST['action'] && check_admin_referer( 'eac_edit_customer' ) && current_user_can( 'eac_manage_customer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$data = array(
				'id'         => isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '',
				'name'       => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'currency'   => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '',
				'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
				'company'    => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
				'tax_number' => isset( $_POST['tax_number'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_number'] ) ) : '',
				'website'    => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
				'address'    => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
				'city'       => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
				'state'      => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
				'zip'        => isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '',
				'country'    => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
			);

			$customer = EAC()->customers->insert( $data );

			if ( is_wp_error( $customer ) ) {
				EAC()->flash->error( $customer->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Customer saved successfully.', 'wp-ever-accounting' ) );
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
				if ( ! EAC()->customers->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve a customer that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Customers();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_customers_per_page',
					)
				);
				break;
		}
	}

	/**
	 * Output the customers page.
	 *
	 * @param string $action Current action.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function page_content( $action ) {
		switch ( $action ) {
			case 'add':
			case 'edit':
				include __DIR__ . '/views/customer-edit.php';
				break;

			case 'view':
				include __DIR__ . '/views/customer-view.php';
				break;

			default:
				include __DIR__ . '/views/customer-list.php';
				break;
		}
	}

	/**
	 * Customer overview.
	 *
	 * @param Customer $customer Customer object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function overview_section( $customer ) {
		?>
		<div class="eac-chart">
			<canvas id="eac-customer-chart" style="min-height: 300px;"></canvas>
		</div>
		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Overdue', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">100$</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Open', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">200$</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">400$</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Customer payments.
	 *
	 * @param Customer $customer Customer object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function payments_section( $customer ) {
		$payments = EAC()->payments->query(
			array(
				'customer_id' => $customer->id,
				'limit'       => 10,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		?>
		<h3><?php esc_html_e( 'Recent Payments', 'wp-ever-accounting' ); ?></h3>
		<table class="widefat fixed striped">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Number', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( $payments ) : ?>
				<?php foreach ( $payments as $payment ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $payment->get_view_url() ); ?>">
								<?php echo esc_html( $payment->number ); ?>
							</a>
						<td><?php echo esc_html( $payment->date ); ?></td>
						<td><?php echo esc_html( $payment->formatted_amount ); ?></td>
						<td><?php echo esc_html( $payment->formatted_status ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Customer invoices.
	 *
	 * @param Customer $customer Customer object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function invoices_section( $customer ) {
		$invoices = EAC()->invoices->query(
			array(
				'customer_id' => $customer->id,
				'limit'       => 10,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		?>
		<h3><?php esc_html_e( 'Recent Invoices', 'wp-ever-accounting' ); ?></h3>
		<table class="widefat fixed striped">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Number', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( $invoices ) : ?>
				<?php foreach ( $invoices as $invoice ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $invoice->get_view_url() ); ?>">
								<?php echo esc_html( $invoice->number ); ?>
							</a>
						<td><?php echo esc_html( $invoice->date ); ?></td>
						<td><?php echo esc_html( $invoice->formatted_amount ); ?></td>
						<td><?php echo esc_html( $invoice->formatted_status ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'No invoices found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Customer notes.
	 *
	 * @param Customer $customer Customer object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function notes_section( $customer ) {
		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $customer->id,
				'parent_type' => 'customer',
				'limit'       => 100,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		?>
		<h3><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h3>
		<form name="customer-note" action="" method="post">
			<div class="eac-form-field">
				<label for="content"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
				<textarea name="content" id="content" cols="30" rows="2" required="required" placeholder="<?php esc_attr_e( 'Enter Note', 'wp-ever-accounting' ); ?>"></textarea>
			</div>
			<?php wp_nonce_field( 'eac_add_customer_note' ); ?>
			<input type="hidden" name="action" value="eac_add_customer_note">
			<input type="hidden" name="parent_id" value="<?php echo esc_attr( $customer->id ); ?>">
			<button class="button"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></button>
		</form>
		<br>
		<?php if ( $notes ) : ?>
			<ul class="eac-notes">
				<?php foreach ( $notes as $note ) : ?>
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( $note->creator_id, 32 ); ?>
								<span class="note__author-name"><?php echo esc_html( get_the_author_meta( 'display_name', $note->creator_id ) ); ?></span>
							</div>
							<div class="note__date">
								<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $note->created_at ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p><?php echo esc_html( $note->content ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p><?php esc_html_e( 'No notes available.', 'wp-ever-accounting' ); ?></p>
		<?php endif; ?>
		<?php
	}
}
