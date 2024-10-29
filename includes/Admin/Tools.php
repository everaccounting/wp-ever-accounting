<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools
 *
 * @package EverAccounting\Admin
 * @since 1.0.0
 */
class Tools {

	/**
	 * Tools constructor.
	 */
	public function __construct() {
		add_filter( 'eac_tools_page_tabs', array( __CLASS__, 'register_tabs' ), -1 );
		add_action( 'admin_init', array( __CLASS__, 'handle_csv_download' ) );
		add_action( 'eac_tools_page_import_content', array( __CLASS__, 'import_content' ) );
		add_action( 'eac_tools_page_export_content', array( __CLASS__, 'export_content' ) );
	}

	/**
	 * Register tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['import'] = __( 'Import', 'wp-ever-accounting' );
		$tabs['export'] = __( 'Export', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Handle CSV download.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function handle_csv_download() {
		check_admin_referer( 'eac_download_csv' );
		$type = isset( $_GET['type'] ) ? sanitize_key( wp_unslash( $_GET['type'] ) ) : '';
	}

	/**
	 * Import tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function import_content() {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">

			</div>
		</div>
		<?php
	}

	/**
	 * Export tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function export_content() {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Export Customers', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">
				<form method="post" class="eac_exporter" data-type="customers" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_ajax_export' ) ); ?>">
					<p><?php esc_html_e( 'Export customers from this site as CSV file. Exported file can be imported into other site.', 'wp-ever-accounting' ); ?></p>
					<?php submit_button( esc_html__( 'Export', 'wp-ever-accounting' ), 'secondary', null, false ); ?>
				</form>
			</div>
		</div>
		<?php
	}
}
