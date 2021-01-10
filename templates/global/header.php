<?php
/**
 * Displays header.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/header.php.
 *
 * @version 1.1.0
 */
?>

<div class="ea-document__top">
	<div class="ea-container">
		<div class="ea-company-logo">
			<img src="<?php echo eaccounting()->settings->get( 'company_logo', eaccounting()->plugin_url( '/assets/images/document-logo.png' ) ); ?>" alt="company-logo" class="company-logo">
		</div>
	</div>
	<!-- /.ea-container -->
</div>
