<div class="ea-columns ea-noprint">
	<div>
		<div class="ea-document__status <?php echo sanitize_html_class($invoice->get_status());?>)>"><span><?php echo esc_html($invoice->get_status_nicename());?></span></div>
	</div>
	<div>
		<button class="button-secondary">Print</button>
	</div>
</div>
