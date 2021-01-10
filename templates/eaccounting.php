<?php
/**
 * Main page for eaccounting.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/eaccounting.php.
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
	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php do_action( 'eaccounting_header' ); ?>
</head>
<body class="eaccounting">
<?php do_action( 'eaccounting_body' ); ?>
<?php do_action( 'eaccounting_body_end' ); ?>
<?php do_action( 'eaccounting_footer' ); ?>
</body>
</html>
