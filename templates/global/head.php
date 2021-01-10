<?php
/**
 * Main page for eaccounting.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/head.php.
 *
 * @version 1.1.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<link rel="profile" href="https://gmpg.org/xfn/11"/>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
	<?php do_action( 'eaccounting_header' ); ?>
</head>
<body <?php body_class(); ?>>

<?php
do_action( 'eaccounting_body_top' );
wp_body_open();

do_action( 'eaccounting_before_header' );

do_action( 'eaccounting_must_head' );

do_action( 'eaccounting_after_header' );
?>

<div id="eaccounting-body" class="eaccounting-content">
	<div class="ea-container">
		<?php do_action( 'eaccounting_before_main_content' ); ?>



