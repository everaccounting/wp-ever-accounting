<?php
/**
 * Admin Revenue View Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
$revenue_id = isset( $_REQUEST['revenue_id'] ) ? absint( $_REQUEST['revenue_id'] ) : null;
try {
	$revenue = new \EverAccounting\Transaction( $revenue_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
if ( $revenue->exists() && 'income' !== $revenue->get_type() ) {
	echo __( 'Unknown revenue ID', 'wp-ever-accounting' );
	exit();
}
$back_url = remove_query_arg( array( 'action', 'revenue_id' ) );
?>

<div class="ea-invoice-card">

	<div class="ea-card ea-invoice-card-header is-compact">
		<h3 class="ea-invoice-card-header-title"><?php echo sprintf( __( 'Revenue #%d', 'wp-ever-accounting' ), $revenue->get_id() ); ?></h3>
	</div>

	<div class="ea-card">
<!--		<div class="ea-flex-row">-->
<!--			<div class="ea-transaction-card-logo">-->
<!--				<img src="--><?php // echo esc_url_raw( eaccounting()->plugin_url( '/assets/images/placeholders/placeholder.png' ) ); ?><!--" alt="company logo">-->
<!--			</div>-->
<!---->
<!--		</div>-->

	</div>

</div>

