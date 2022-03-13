<?php
/**
 * Displays a Invoice.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/invoice.php.
 *
 * @var $invoice Invoice
 * @version 1.1.0
 */

use Ever_Accounting\Invoice;
use Ever_Accounting\Helpers\Template;

defined( 'ABSPATH' ) || exit;
?>
<div class="ea-document ea-invoice">

	<div class="ea-document__section">
		<div class="ea-document__column alignleft">
			<h4 class="ea-document__number"><?php echo esc_html( $invoice->get_invoice_number() ); ?></h4>
			<?php Template::get_template( 'invoice/company-info.php', array( 'invoice' => $invoice ) ); ?>
		</div>
		<div class="ea-document__column alignright">
			<span class="ea-document__to"><?php esc_html_e( 'Invoice To:', 'wp-ever-accounting' ); ?></span>
			<?php Template::get_template( 'invoice/invoice-to.php', array( 'invoice' => $invoice ) ); ?>
			<?php Template::get_template( 'invoice/invoice-meta.php', array( 'invoice' => $invoice ) ); ?>
		</div>
	</div>

	<?php Template::get_template( 'invoice/invoice-items.php', array( 'invoice' => $invoice ) ); ?>
	<?php Template::get_template( 'invoice/invoice-note.php', array( 'invoice' => $invoice ) ); ?>
	<?php Template::get_template( 'invoice/invoice-terms.php', array( 'invoice' => $invoice ) ); ?>

</div>
