<?php
/**
 * EverAccounting Admin Overview Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Overview {
	/**
	 * EAccounting_Admin_Overview constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 1 );
	}

	/**
	 * Registers the overview page.
	 *
	 * @since 1.1.0
	 */
	public function register_page() {
		global $menu;

		if ( current_user_can( 'manage_eaccounting' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icons = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( eaccounting()->plugin_path( 'assets/images/icon.svg' ) ) );

		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			null,
			$icons,
			'54.5'
		);
		$overview = add_submenu_page(
			'eaccounting',
			__( 'Overview', 'wp-ever-accounting' ),
			__( 'Overview', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			array( $this, 'render_page' )
		);
		//      error_log($overview);
		add_action( 'load-' . $overview, array( __CLASS__, 'eaccounting_dashboard_setup' ) );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		include dirname( __FILE__ ) . '/views/admin-page-overview.php';
	}

	public static function eaccounting_dashboard_setup() {
		add_meta_box( 'total-income', false, array( __CLASS__, 'render_total_income_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'total-expense', false, array( __CLASS__, 'render_total_expense_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'total-profit', false, array( __CLASS__, 'render_total_profit_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'cash-flow', __( 'Cash Flow', 'wp-ever-accounting' ), '__return_null', 'ea-overview', 'middle', 'high', array( 'col' => '12' ) );
		add_meta_box( 'income-category-chart', __( 'Income By Categories', 'wp-ever-accounting' ), '__return_null', 'ea-overview', 'advanced', 'high', array( 'col' => '12' ) );
		add_meta_box( 'expense-category-chart', __( 'Expense By Categories', 'wp-ever-accounting' ), '__return_null', 'ea-overview', 'advanced', 'high', array( 'col' => '12' ) );
		add_meta_box( 'latest-income', __( 'Latest Incomes', 'wp-ever-accounting' ), '__return_null', 'ea-overview' );
		add_meta_box( 'latest-expense', __( 'Latest Expenses', 'wp-ever-accounting' ), '__return_null', 'ea-overview' );
		add_meta_box( 'account-balance', __( 'Account Balances', 'wp-ever-accounting' ), '__return_null', 'ea-overview' );
		do_action( 'eaccounting_dashboard_setup' );
	}

	public static function render_total_income_widget() {
		global $wpdb;
		$total_income = get_transient( 'eaccounting_widget_total_income' );
		if ( empty( $total_income ) ) {
			$sql          = $wpdb->prepare(
				"
										SELECT Sum(amount) amount,
								   currency_code,
								   currency_rate
							FROM   {$wpdb->prefix}ea_transactions
							WHERE  type = %s
								   AND category_id NOT IN (SELECT id
														   FROM   {$wpdb->prefix}ea_categories
														   WHERE  type = 'other')
							GROUP  BY currency_code,
									  currency_rate
			",
				'income'
			);
			$results      = $wpdb->get_results( $sql );
			$total_income = 0;
			foreach ( $results as $result ) {
				$total_income += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_income', $total_income, MINUTE_IN_SECONDS * 60 );
		}

		$total_receivable = get_transient( 'eaccounting_widget_total_receivable' );
		if ( empty( $total_receivable ) ) {
			$sql = $wpdb->prepare(
				"
			SELECT Sum(amount) amount,
				   currency_code,
				   currency_rate
			FROM   {$wpdb->prefix}ea_transactions
			WHERE  type = %s
				   AND document_id IN (SELECT id
									   FROM   {$wpdb->prefix}ea_documents
									   WHERE  status NOT IN ( 'draft', 'cancelled' )
											  AND `status` <> 'paid'
											  AND type = 'invoice')
			GROUP  BY currency_code,
					  currency_rate
			",
				'income'
			);

			$results          = $wpdb->get_results( $sql );
			$total_receivable = 0;
			foreach ( $results as $result ) {
				$total_receivable += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_receivable', $total_receivable, MINUTE_IN_SECONDS * 60 );
		}

		?>
		<div class="ea-score-card__inside">
			<div class="ea-score-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-score-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Sales', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_income ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Receivable', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_receivable ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_total_expense_widget() {
		global $wpdb;
		$total_expense = get_transient( 'eaccounting_widget_total_expense' );
		if ( empty( $total_expense ) ) {
			$sql           = $wpdb->prepare(
				"
										SELECT Sum(amount) amount,
								   currency_code,
								   currency_rate
							FROM   {$wpdb->prefix}ea_transactions
							WHERE  type = %s
								   AND category_id NOT IN (SELECT id
														   FROM   {$wpdb->prefix}ea_categories
														   WHERE  type = 'other')
							GROUP  BY currency_code,
									  currency_rate
			",
				'expense'
			);
			$results       = $wpdb->get_results( $sql );
			$total_expense = 0;
			foreach ( $results as $result ) {
				$total_expense += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_expense', $total_expense, MINUTE_IN_SECONDS * 1 );
		}
		$total_payable = get_transient( 'eaccounting_widget_total_payable' );
		if ( empty( $total_payable ) ) {
			$sql = $wpdb->prepare(
				"
			SELECT Sum(amount) amount,
				   currency_code,
				   currency_rate
			FROM   {$wpdb->prefix}ea_transactions
			WHERE  type = %s
				   AND document_id IN (SELECT id
									   FROM   {$wpdb->prefix}ea_documents
									   WHERE  status NOT IN ( 'draft', 'cancelled' )
											  AND `status` <> 'paid'
											  AND type = 'bill')
			GROUP  BY currency_code,
					  currency_rate
			",
				'expense'
			);

			$results       = $wpdb->get_results( $sql );
			$total_payable = 0;
			foreach ( $results as $result ) {
				$total_payable += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_payable', $total_payable, MINUTE_IN_SECONDS * 60 );
		}
		?>
		<div class="ea-widget-card alert">
			<div class="ea-widget-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-widget-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Expenses', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_expense ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Payable', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_payable ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_total_profit_widget() {
		$total_income     = (float) get_transient( 'eaccounting_widget_total_income' );
		$total_expense    = (float) get_transient( 'eaccounting_widget_total_expense' );
		$total_receivable = (float) get_transient( 'eaccounting_widget_total_receivable' );
		$total_payable    = (float) get_transient( 'eaccounting_widget_total_payable' );
		$total_profit     = $total_income - $total_expense;
		$total_upcoming   = $total_receivable - $total_payable;
		?>
		<div class="ea-widget-card success">
			<div class="ea-widget-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-widget-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_profit ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Upcoming', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_upcoming ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}
}

return new EAccounting_Admin_Overview();
