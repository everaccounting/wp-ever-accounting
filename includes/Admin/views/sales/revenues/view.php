<?php
/**
 * Admin Veiw Revenue.
 * Page: Sales
 * Tab: Revenue
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $revenue \EverAccounting\Models\Revenue Revenue object.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="eac-columns">
	<div class="eac-col-9">
		<div class="bkit-panel">
			<?php echo do_shortcode( '[eac_revenue id=' . $revenue->id . ']' ); ?>
		</div>
	</div>
	<div class="eac-col-3"></div>
</div>


