<?php
/**
 * Render invoices tab contents
 */
defined( 'ABSPATH' ) || exit();
$invoices_page_url = admin_url( 'admin.php?page=eaccounting-income' );
$title             = __( 'Add Invoice', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $invoices_page_url, __( 'All Invoices', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form action="">
		<div class="ea-row">
			<?php

			echo eaccounting_input_field( array(
				'label'         => __( 'Date', 'wp-ever-accounting' ),
				'name'          => 'paid_at',
				'value'         => isset( $account['paid_at'] ) ? $account['paid_at'] : '',
				'placeholder'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Amount', 'wp-ever-accounting' ),
				'name'          => 'amount',
				'class'          => 'ea-price',
				'value'         => isset( $account['amount'] ) ? $account['amount'] : '',
				'placeholder'   => '100',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_accounts_dropdown();




			echo eaccounting_input_field( array(
				'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
				'name'          => 'invoice_number',
				'value'         => isset( $account['invoice_number'] ) ? $account['invoice_number'] : '',
				'placeholder'   => __( 'INV-0001', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-file-text-o',
				'required'      => true,
				'readonly'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
				'name'          => 'issued_at',
				'value'         => isset( $account['issued_at'] ) ? $account['issued_at'] : '',
				'placeholder'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Due Date', 'wp-ever-accounting' ),
				'name'          => 'issued_at',
				'value'         => isset( $account['due_at'] ) ? $account['due_at'] : '',
				'placeholder'   => date( 'Y-m-d', strtotime( '+15 days' ) ),
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Category', 'wp-ever-accounting' ),
				'name'          => 'category',
				'value'         => isset( $account['category'] ) ? $account['category'] : '',
				'placeholder'   => '',
				'icon'          => 'fa fa-folder',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Order Number', 'wp-ever-accounting' ),
				'name'          => 'order_number',
				'value'         => isset( $account['order_number'] ) ? $account['order_number'] : '',
				'placeholder'   => '',
				'icon'          => 'fa fa-shopping-cart',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
		</div>

	</form>
</div>
