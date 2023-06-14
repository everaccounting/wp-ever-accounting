<div class="eac-container">
	<div class="eac-panel padding-6">
		<div class="eac-columns display-flex align-items-center border-bottom padding-bottom-3 margin-bottom-3">
			<div class="eac-col-6">
				<div class="eac-document__logo">
					<img src="https://byteever.com/wp-content/plugins/wp-ever-accounting/dist/images/document-logo.png" alt="ByteEver">
				</div>
			</div>
			<div class="eac-col-6 text-end-md">
				<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
			</div>
		</div>
		<div class="eac-columns margin-bottom-6">
			<div class="eac-col-6">
				<div class="eac-document__from">
					<strong>Invoice to</strong><br>
					<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
				</div>
			</div>
			<div class="eac-col-6">
				<div class="eac-document__to text-end">
					<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
				</div>
			</div>
		</div>

	</div>
</div>
