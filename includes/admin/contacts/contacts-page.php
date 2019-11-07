<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_contacts_page() {
	wp_enqueue_script('eaccounting-contacts');

	eaccounting_page_wrapper_open('contacts-page');
	if ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'edit_contact' ) {
		require_once dirname( __FILE__ ) . '/add-contact.php';
	} elseif ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'add_contact' ) {
		require_once dirname( __FILE__ ) . '/add-contact.php';
	} else {
		require_once dirname( __FILE__ ) . '/class-contacts-table.php';
//		$contacts_table = new EAccounting_Products_Table();
//		$contacts_table->prepare_items();
		$base_url = admin_url('admin.php?page=eaccounting-contacts');
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Contacts', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting_action' => 'add_contact' ), $base_url ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<hr class="wp-header-end">


		<?php do_action( 'eaccounting_contacts_page_top' ); ?>
		<form id="eaccounting-contacts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=eaccounting-accounts' ); ?>">
			<?php //$contacts_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="eaccounting-contacts" />

			<?php //$contacts_table->views() ?>
			<?php //$contacts_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_contact_page_bottom' );
	}
	eaccounting_page_wrapper_close();
}
