<?php
/**
 * Edit invoice view.
 *
 * @package EverAccounting
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="eac-poststuff">
	<div class="column-1">
		<?php echo do_shortcode( '[eac_invoice id="' . absint( $invoice->id ) . '"]' ); ?>
	</div>
	<div class="column-2">

	</div>
</div>

