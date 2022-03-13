<?php
/**
 * Template for displaying footer.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/head.php.
 *
 * @version 1.1.0
 */

use Ever_Accounting\Helpers\Misc;

defined( 'ABSPATH' ) || exit;
$logo      = ever_accounting_get_option( 'company_logo' );
$site_name = Misc::get_site_name();
?>
<header class="ea-header ea-clearfix ea-noprint">
	<div class="ea-container">
		<div class="ea-row">
			<div class="ea-col-3">
				<?php if ( ! empty( $logo ) ) : ?>
					<a class="ea-brand" href="<?php echo esc_url( site_url() ); ?>">
						<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" height="100" width="100">
					</a>
				<?php else : ?>
				<h1 class="ea-site-title"><?php esc_html_e( $site_name,'wp-ever-accounting' ); ?></h1>
				<?php endif; ?>
			</div>
			<div class="ea-col-9"></div>
		</div>
	</div>
</header>
<div class="ea-body">
