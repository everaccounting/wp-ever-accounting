<?php
/**
 * Admin View: Accounts List
 *
 * @var object $list_table
 */

defined( 'ABSPATH' ) || exit;
$add_url    = add_query_arg(
	array(
		'page'   => 'ea-banking',
		'tab'    => 'accounts',
		'action' => 'add',
	),
	admin_url( 'admin.php' )
);
$import_url = add_query_arg(
	array(
		'page' => 'ea-tools',
		'tab'  => 'import',
	),
	admin_url( 'admin.php' )
);
?>
	<h1>
		<?php esc_html_e( 'Accounts', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<a class="page-title-action" href=" <?php echo esc_url( $import_url ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
	</h1>
	<form method="post" id="mainform" action="">
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'account' ); ?>
		<?php $list_table->display(); ?>

		<input type="hidden" name="page" value="ea-banking"/>
		<input type="hidden" name="tab" value="accounts"/>
	</form>

<?php
eaccounting_enqueue_js(
	"
	jQuery( '.wp-list-table [name=\"enabled\"]' ).on( 'change', function(e){
				e.preventDefault();
				var nonce = jQuery(this).data('nonce'),
					data = {
						action: 'eaccounting_edit_account',
						id: jQuery(this).data('id'),
						enabled: jQuery(this).is(':checked'),
						_wpnonce: '" . wp_create_nonce( 'ea_edit_account' ) . " '
					};
				jQuery.post(ajaxurl, data, function (json) {
					jQuery.eaccounting_notice(json);
				});
	} );
	jQuery( '.wp-list-table .row-actions delete a' ).click( function() {
		if ( window.confirm('" . esc_js( __( 'Are you sure you want to clear all logs from the database?', 'woocommerce' ) ) . "') ) {
			return true;
		}
		return false;
	});

	"

);
