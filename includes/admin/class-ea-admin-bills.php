<?php
/**
 * Admin Bill Page
 *
 * Functions used for displaying bill related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Bills {
	/**
	 * EAccounting_Admin_Bill constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_expenses_page_tab_bills', array( $this, 'render_tab' ), 20 );
		add_action( 'eaccounting_admin_bill_header', array( __CLASS__, 'bill_header' ) );
		add_action( 'eaccounting_admin_bill_details', array( __CLASS__, 'bill_details' ) );
		add_action( 'eaccounting_admin_bill_line_items', array( __CLASS__, 'bill_line_items' ) );
		add_action( 'eaccounting_admin_bill_footer', array( __CLASS__, 'bill_footer' ) );
	}

	/**
	 *
	 * @since 1.1.0
	 */
	public function render_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['bill_id'] ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			$this->view_bill( $bill_id );
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$bill_id = isset( $_GET['bill_id'] ) ? absint( $_GET['bill_id'] ) : null;
			$this->edit_bill( $bill_id );
		} else {
			include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-bill-list-table.php' );
			$bill_table = new EAccounting_Bill_List_Table();
			$bill_table->prepare_items();
			$add_url = eaccounting_admin_url(
				array(
					'page'   => 'ea-expenses',
					'tab'    => 'bills',
					'action' => 'add',
				)
			);
			?>
			<h1 class="wp-heading-inline"><?php _e( 'Bills', 'wp-ever-accounting' ); ?></h1>
			<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
			<?php do_action( 'eaccounting_bills_table_top' ); ?>
			<form id="ea-bills-table" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
				<?php
				$bill_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-bills' );
				$bill_table->display();
				?>
				<input type="hidden" name="page" value="ea-expenses"/>
				<input type="hidden" name="tab" value="bills"/>
			</form>
			<?php do_action( 'eaccounting_bills_table_bottom' ); ?>
			<?php
			eaccounting_enqueue_js(
				"jQuery('.del').on('click',function(e){
							if(confirm('Are you sure you want to delete?')){
								return true;
							} else {
								return false;
							}
						});"
			);

		}
	}

	/**
	 * View bill.
	 *
	 * @param $bill_id
	 * @since 1.1.0
	 */
	public function view_bill( $bill_id ) {
		try {
			$bill = new \EverAccounting\Models\Bill( $bill_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}

		if ( empty( $bill ) || ! $bill->exists() ) {
			wp_die( __( 'Sorry, Bill does not exist', 'wp-ever-accounting' ) );
		}
		?>
		<div id="ea-bill" class="columns-2">

			<div class="ea-admin-page__content">
				<div class="ea-card">
					<div class="ea-card__header">
						<h3 class="ea-card__title">
							<?php esc_html_e( 'Bill', 'wp-ever-accounting' ); ?>
						</h3>
						<div>
							<button onclick="history.go(-1);" class="button-secondary"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
						</div>
					</div>

					<div class="ea-card__body">
						<div class="ea-document__watermark">
							<p>
								<?php echo esc_html( $bill->get_status_nicename() ); ?>
							</p>
						</div>

						<?php do_action( 'eaccounting_admin_bill_header', $bill ); ?>
						<?php do_action( 'eaccounting_admin_bill_details', $bill ); ?>
						<?php do_action( 'eaccounting_admin_bill_line_items', $bill ); ?>
						<?php do_action( 'eaccounting_admin_bill_footer', $bill ); ?>

					</div>

				</div>

				<?php eaccounting_do_meta_boxes( 'ea_bill', 'advanced', $bill ); ?>
			</div><!--.ea-admin-page__content-->


			<div class="ea-admin-page__sidebar">
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'side', $bill ); ?>
			</div><!--.ea-admin-page__sidebar-->


		</div><!-- #ea-bill-->
		<?php
	}

	/**
	 * Bill header.
	 *
	 * @param $bill
	 * @since 1.1.0
	 */
	public static function bill_header( $bill ) {
		$company_logo = eaccounting()->settings->get( 'company_logo' );
		$site_name    = wp_parse_url( site_url() )['host'];
		?>
		<div class="ea-card__inside">
			<div class="ea-document__header">
				<div class="ea-document__logo">
					<?php if ( ! empty( $company_logo ) ) : ?>
						<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php echo esc_attr( $site_name ); ?>">
					<?php else : ?>
						<h2><?php echo esc_html( $site_name ); ?></h2>
					<?php endif; ?>
				</div>

				<div class="ea-document__title"><?php _e( 'Bill', '' ); ?></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render bill details.
	 *
	 * @since 1.1.0
	 *
	 * @param \EverAccounting\Models\Bill $bill
	 */
	public static function bill_details( $bill ) {
		$company_address = eaccounting_format_address(
			array(
				'street'   => eaccounting()->settings->get( 'company_address' ),
				'city'     => eaccounting()->settings->get( 'company_city' ),
				'state'    => eaccounting()->settings->get( 'company_state' ),
				'postcode' => eaccounting()->settings->get( 'company_postcode' ),
				'country'  => eaccounting()->settings->get( 'company_country' ),
			)
		);
		$company_name    = eaccounting()->settings->get( 'company_name' );
		$vat_number      = eaccounting()->settings->get( 'company_vat_number' );
		$vendor_address  = eaccounting_format_address( $bill->get_address() );
		?>
		<div class="ea-card__inside">
			<div class="ea-document__details">
				<div class="ea-document__details-column">
					<table class="ea-document__address-table">
						<tbody>
						<tr>
							<th>
								<?php _e( 'To', 'wp-ever-accounting' ); ?>
							</th>
							<td class="spacer-col">&nbsp;</td>
							<td>
								<div class="ea-document__company-name">
									<?php echo empty( $company_name ) ? '&mdash;' : esc_html( $company_name ); ?>
								</div>
								<div class="ea-document__company-address">
									<?php echo $company_address; ?><br>
									<?php _e( 'VAT#', 'wp-ever-accounting' ); ?> <?php echo empty( $vat_number ) ? '&mdash;' : esc_html( $vat_number ); ?>
								</div>
							</td>
						</tr>
						<tr>
							<th>
								<?php _e( 'From', 'wp-ever-accounting' ); ?>
							</th>
							<td class="spacer-col">&nbsp;</td>
							<td>
								<div class="ea-document__contact-name">
									<?php echo empty( $bill->get_name() ) ? '&mdash;' : esc_html( $bill->get_name() ); ?>
								</div>
								<div class="ea-document__contact-address">
									<?php echo $vendor_address; ?><br/>
									<?php _e( 'VAT#', 'wp-ever-accounting' ); ?> <?php echo empty( $bill->get_vat_number() ) ? '&mdash;' : esc_html( $bill->get_vat_number() ); ?>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="ea-document__details-column">
					<table class="ea-document__meta-table">
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Bill Number', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_bill_number() ) ? '&mdash;' : esc_html( $bill->get_bill_number( 'view' ) ); ?>
							</td>
						</tr>
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Order Number', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_order_number() ) ? '&mdash;' : esc_html( $bill->get_order_number( 'view' ) ); ?>
							</td>
						</tr>
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Bill Date', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_issue_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_issue_date(), 'M j, Y' ); ?>
							</td>
						</tr>
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Payment Date', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_payment_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_payment_date(), 'M j, Y' ); ?>
							</td>
						</tr>
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Due Date', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_due_date() ) ? '&mdash;' : eaccounting_format_datetime( $bill->get_due_date(), 'M j, Y' ); ?>
							</td>
						</tr>
						<tr>
							<th class="ea-document__meta-label"><?php _e( 'Bill Status', 'wp-ever-accounting' ); ?></th>
							<td class="spacer-col">&nbsp;</td>
							<td class="ea-document__meta-content">
								<?php echo empty( $bill->get_status() ) ? '&mdash;' : esc_html( $bill->get_status_nicename() ); ?>
							</td>
						</tr>
					</table>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render bill line items.
	 *
	 * @since 1.1.0
	 *
	 * @param \EverAccounting\Models\Bill $bill
	 */
	public static function bill_line_items( $bill ) {
		eaccounting_get_admin_template( 'bills/bill-items', array( 'bill' => $bill ) ); //phpcs:ignore
	}


	public static function bill_footer( $bill ) {

	}

	public function edit_bill( $bill_id ) {
		try {
			$bill = new \EverAccounting\Models\Bill( $bill_id );
		} catch ( Exception $e ) {
			wp_die( $e->getMessage() );
		}
		$bill->maybe_set_document_number();
		$title    = $bill->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );
		$view_url = admin_url( 'admin.php' ) . '?page=ea-sales&tab=bills&action=view&bill_id=' . $bill->get_id();
		?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis, ut!
					</div>

					<div id="postbox-container-1" class="postbox-container">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, maiores.
					</div>
					
				</div>
			</div>
		<?php
	}
}

return new EAccounting_Admin_Bills();
