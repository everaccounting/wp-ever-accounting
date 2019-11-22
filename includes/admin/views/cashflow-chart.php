<?php
// Monthly
$labels = array();
if ($range == 'last_12_months') {
	$end_month   = 12;
	$start_month = 0;
} elseif ($range == 'custom') {
	$end_month   = $end->diffInMonths($start);
	$start_month = 0;
}


?>
<div class="ea-col-12">
	<div class="ea-card">
		<div class="ea-card-header">
			<h3 class="ea-card-title"><?php _e( 'Cash Flow', 'wp-ever-accounting' ); ?></h3>
		</div>
		<div class="ea-card-body">
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni, quis!
		</div>
	</div>
</div>
