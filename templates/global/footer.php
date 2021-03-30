<?php
/**
 * Main page for eaccounting.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/page-end.php.
 *
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;
$host = eaccounting_get_site_name();
?>
</div><!--/ea-container-->
</div><!--/ea-body-->
<footer class="ea-footer ea-noprint">
	<div class="ea-container">
		<p class="ea-copyright-info">
			<?php echo date_i18n( 'Y' ); ?>
			<?php echo sprintf(esc_html__('Copyright %s', 'wp-ever-accounting'), $host);?>
		</p>
	</div>
</footer>

</body>

</html>
