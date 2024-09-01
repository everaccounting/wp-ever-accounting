<?php

namespace EverAccounting\Admin\Sales;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payments
 *
 * @package EverAccounting\Admin\Sales
 */
class Payments {

	/**
	 * List table object.
	 *
	 * @since 1.0.0
	 * @var PaymentsTable
	 */
	protected $list_table;

	/**
	 * Payments constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'setup_home_screen' ) );
		add_action( 'load_eac_sales_page_payments_home', array( $this, 'home_screen' ) );
		add_action( 'eac_sales_page_payments_home', array( $this, 'render_list' ) );
		add_action( 'eac_sales_page_payments_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_payments_edit', array( $this, 'render_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function setup_home_screen( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Load payments list.
	 *
	 * @param \WP_Screen $screen Screen object.
	 *
	 * @since 1.0.0
	 */
	public function home_screen() {
		$screen           = get_current_screen();
		$this->list_table = new PaymentsTable();
		$this->list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Payments', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => 'eac_payments_per_page',
		) );
	}

	/**
	 * Render payments table.
	 *
	 * @since 1.0.0
	 */
	public function render_list() {
		?>
		<h1 class="wp-heading-inline">
			<?php esc_html_e( 'Payments', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-sales&tab=payments&action=add' ) ); ?>" class="button button-small">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
			<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="button button-small">
				<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
			</a>
			<?php if ( $this->list_table->get_request_search() ) : ?>
				<?php // translators: %s: search query. ?>
				<span class="subtitle"><?php echo esc_html( sprintf( __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $this->list_table->get_request_search() ) ) ); ?></span>
			<?php endif; ?>
		</h1>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
			<?php $this->list_table->views(); ?>
			<?php $this->list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
			<?php $this->list_table->display(); ?>
			<input type="hidden" name="page" value="eac-sales"/>
			<input type="hidden" name="tab" value="payments"/>
		</form>
		<?php
	}

	/**
	 * Render payments add.
	 *
	 * @since 1.0.0
	 */
	public function render_add() {
		echo 'Add payment';
	}

	/**
	 * Render payments edit.
	 *
	 * @since 1.0.0
	 */
	public function render_edit() {
		echo 'Edit payment';
	}
}
