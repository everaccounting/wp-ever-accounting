<?php
/**
 * Main page for eaccounting.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/page-start.php.
 *
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;

$logo      = eaccounting()->settings->get( 'company_logo' );
$site_name = eaccounting_get_site_name();

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<link rel="profile" href="https://gmpg.org/xfn/11"/>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php do_action( 'eaccounting_head' ); ?>
</head>

<body <?php body_class('wp-core-ui eaccounting');?>>

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
	<div class="ea-container">
