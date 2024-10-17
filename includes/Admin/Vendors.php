<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendors
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Purchases
 */
class Vendors {
	/**
	 * Vendors constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'admin_post_eac_edit_vendor', array( __CLASS__, 'handle_edit' ) );
		add_action( 'eac_purchases_page_vendors_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_purchases_page_vendors_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_vendor_profile_section_overview', array( __CLASS__, 'overview_section' ) );
		add_action( 'eac_vendor_profile_section_expenses', array( __CLASS__, 'expenses_section' ) );
		add_action( 'eac_vendor_profile_section_bills', array( __CLASS__, 'bills_section' ) );
		add_action( 'eac_vendor_profile_section_notes', array( __CLASS__, 'notes_section' ) );
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
		if ( current_user_can( 'eac_manage_vendor' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['vendors'] = __( 'Vendors', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle actions.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_vendor' );
		if ( ! current_user_can( 'eac_manage_vendor' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			wp_die( esc_html__( 'You do not have permission to edit vendors.', 'wp-ever-accounting' ) );
		}
		$referer = wp_get_referer();
		$data    = array(
			'id'         => isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '',
			'name'       => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'company'    => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
			'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'website'    => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
			'address'    => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
			'city'       => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
			'state'      => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
			'postcode'   => isset( $_POST['postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) : '',
			'country'    => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
			'tax_number' => isset( $_POST['tax_number'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_number'] ) ) : '',
			'currency'   => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '',
		);

		$vendor = EAC()->vendors->insert( $data );

		if ( is_wp_error( $vendor ) ) {
			EAC()->flash->error( $vendor->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Vendor saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'id', $vendor->id, $referer );
			$referer = add_query_arg( 'action', 'view', $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
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
				if ( ! EAC()->vendors->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve a vendor that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Vendors();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_vendors_per_page',
					)
				);
				break;
		}
	}

	/**
	 * Output the vendors page.
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
				include __DIR__ . '/views/vendor-edit.php';
				break;

			case 'view':
				include __DIR__ . '/views/vendor-view.php';
				break;

			default:
				include __DIR__ . '/views/vendor-list.php';
				break;
		}
	}

	/**
	 * Vendor overview.
	 *
	 * @param Vendor $vendor Vendor object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function overview_section( $vendor ) {
		global $wpdb;
		$stats = apply_filters(
			'eac_vendor_overview_stats',
			array(
				array(
					'label' => __( 'Receivable', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),

				array(
					'label' => __( 'Payable', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),

				array(
					'label' => __( 'Upcoming', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),
			)
		);
		?>
		<h2 class="has--border"><?php echo esc_html__( 'Overview', 'wp-ever-accounting' ); ?></h2>
		<canvas class="eac-profile-cart" style="min-height: 300px;"></canvas>
		<div class="eac-stats stats--3">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="eac-stat">
					<div class="eac-stat__label"><?php echo esc_html( $stat['label'] ); ?></div>
					<div class="eac-stat__value">
						<?php echo esc_html( $stat['value'] ); ?>
						<?php if ( isset( $stat['delta'] ) ) : ?>
							<?php $delta_class = $stat['delta'] > 0 ? 'is--positive' : 'is--negative'; ?>
							<div class="eac-stat__delta <?php echo esc_attr( $delta_class ); ?>">
								<?php echo esc_html( $stat['delta'] ); ?>%
							</div>
						<?php endif; ?>
					</div>
					<?php if ( isset( $stat['meta'] ) ) : ?>
						<div class="eac-stat__meta">
							<span><?php echo wp_kses_post( implode( ' </span><span> ', $stat['meta'] ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>

		<h4 class="tw-text-gray-500 tw-uppercase tw-text-base">Basic Info</h4>
		<div class="tw-grid tw-gap-4 tw-mt-5 md:tw-grid-cols-2 lg:tw-grid-cols-3">
			<div>
				<label class="tw-mb-1">Display Name</label>
				<p class="tw-font-bold">admin </p>
			</div>
			<div>
				<label class="tw-mb-1">Primary Contact Name</label>
				<p class="tw-font-bold">Primary Contact </p>
			</div>
			<div>
				<label class="tw-mb-1">Email</label>
				<p class="tw-font-bold">email@gmail.com </p>
			</div>
			<div>
				<label class="tw-mb-1">Display Name</label>
				<p class="tw-font-bold">admin </p>
			</div>
			<div>
				<label class="tw-mb-1">Primary Contact Name</label>
				<p class="tw-font-bold">Primary Contact </p>
			</div>
			<div>
				<label class="tw-mb-1">Email</label>
				<p class="tw-font-bold">email@gmail.com </p>
			</div>
		</div>
		<?php
	}

	/**
	 * Vendor expenses.
	 *
	 * @param Vendor $vendor Vendor object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function expenses_section( $vendor ) {
		$expenses = EAC()->expenses->query(
			array(
				'vendor_id' => $vendor->id,
				'orderby'   => 'created_at',
				'order'     => 'DESC',
				'limit'     => 20,
			)
		);
		?>
		<h2 class="has--border"><?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?></h2>
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
			<?php if ( $expenses ) : ?>
				<?php foreach ( $expenses as $expense ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $expense->get_view_url() ); ?>">
								<?php echo esc_html( $expense->number ); ?>
							</a>
						</td>
						<td><?php echo esc_html( wp_date( eac_date_format(), strtotime( $expense->date ) ) ); ?></td>
						<td><?php echo esc_html( eac_format_amount( $expense->amount ) ); ?></td>
						<td><?php echo esc_html( $expense->status ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'No expenses found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Vendor bills.
	 *
	 * @param Vendor $vendor Vendor object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function bills_section( $vendor ) {
		$bills = EAC()->bills->query(
			array(
				'vendor_id' => $vendor->id,
				'orderby'   => 'created_at',
				'order'     => 'DESC',
				'limit'     => 20,
			)
		);
		?>
		<h2 class="has--border"><?php esc_html_e( 'Recent Bills', 'wp-ever-accounting' ); ?></h2>
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
			<?php if ( $bills ) : ?>
				<?php foreach ( $bills as $bill ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( $bill->get_view_url() ); ?>">
								<?php echo esc_html( $bill->number ); ?>
							</a>
						</td>
						<td><?php echo esc_html( wp_date( eac_date_format(), strtotime( $bill->issue_date ) ) ); ?></td>
						<td><?php echo esc_html( eac_format_amount( $bill->amount ) ); ?></td>
						<td><?php echo esc_html( $bill->status ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'No bills found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Vendor notes.
	 *
	 * @param Vendor $vendor Vendor object.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function notes_section( $vendor ) {
		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $vendor->id,
				'parent_type' => 'vendor',
				'orderby'     => 'created_at',
				'order'       => 'DESC',
				'limit'       => 20,
			)
		);
		?>
		<h2 class="has--border"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h2>
		<div class="eac-form-field">
			<label for="eac-note"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
			<textarea id="eac-note" cols="30" rows="2" placeholder="<?php esc_attr_e( 'Enter Note', 'wp-ever-accounting' ); ?>"></textarea>
		</div>
		<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $vendor->id ); ?>" data-parent_type="vendor" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
			<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
		</button>

		<?php include __DIR__ . '/views/note-list.php'; ?>
		<?php
	}
}
