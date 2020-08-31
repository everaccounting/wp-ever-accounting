<?php
/**
 * Admin Tools Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Tools
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
/**
 * render tools page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_tools_page() {
	$tabs       = eaccounting_get_tools_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'accounts' );

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php eaccounting_navigation_tabs( $tabs, $active_tab ); ?>
		</h2>
		<div id="tab_container">
			<?php
			/**
			 * Fires in the Tabs screen tab.
			 *
			 * The dynamic portion of the hook name, `$active_tab`, refers to the slug of
			 * the currently active tools tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_tools_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve tools tabs
 *
 * @since 1.0.2
 * @return array $tabs
 */
function eaccounting_get_tools_tabs() {
	$tabs                = array();
	$tabs['import']      = __( 'Import', 'wp-ever-accounting' );
	$tabs['export']      = __( 'Export', 'wp-ever-accounting' );
	$tabs['system_info'] = __( 'System Info', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_tools_tabs', $tabs );
}

/**
 * Setup tools pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_tools_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'import' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_tools_page_tab' . $tab );
}

function eaccounting_export_tab() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Customers', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			<form method="post" enctype="multipart/form-data" class="ea-batch-form" data-process_name="export-customers" data-nonce="<?php echo esc_attr( wp_create_nonce( 'export-customers_step_nonce' ) ); ?>">
				<?php echo sprintf( '<p>%s</p>', __( 'Export customers from this site as CSV file. Exported file can be imported into other site.', 'wp-ever-accounting' ) ); ?>
				<?php submit_button( __( 'Export', 'wp-ever-accounting' ), 'secondary', 'export-customers-submit', false ); ?>
			</form>
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Vendors', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Accounts', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Currencies', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Categories', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Transactions', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Export Settings', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<?php
}

add_action( 'eaccounting_tools_tab_export', 'eaccounting_export_tab' );


function eaccounting_tools_import_tab() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Customers', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">

		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Vendors', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Accounts', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Currencies', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Categories', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Transactions', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php _e( 'Import Settings', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. In, veniam?
		</div>
	</div>

	<?php
}

add_action( 'eaccounting_tools_tab_import', 'eaccounting_tools_import_tab' );


/**
 * System Info tab.
 *
 * @since 1.0.2
 */
function eaccounting_system_info_tab() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$action_url = eaccounting_admin_url( array( 'tab' => 'system_info' ) );
	?>
	<form action="<?php echo esc_url( $action_url ); ?>" method="post" dir="ltr">
		<textarea readonly="readonly" onclick="this.focus(); this.select()" id="ea-system-info-textarea" name="ea-sysinfo" title="<?php esc_attr_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'wp-ever-accounting' ); ?>">
			<?php echo affwp_tools_system_info_report(); ?>
		</textarea>
		<p class="submit">
			<input type="hidden" name="eaccounting_action" value="download_sysinfo"/>
			<?php submit_button( 'Download System Info File', 'primary', 'ea-download-sysinfo', false ); ?>
		</p>
	</form>
	<?php
}

add_action( 'eaccounting_tools_tab_system_info', 'eaccounting_system_info_tab' );
