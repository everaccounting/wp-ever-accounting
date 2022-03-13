<?php
/**
 * Main page for eaccounting.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/eaccounting.php.
 *
 * @version 1.1.0
 */

use Ever_Accounting\Helpers\Template;

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<link rel="profile" href="https://gmpg.org/xfn/11"/>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php do_action( 'ever_accounting_head' ); ?>
</head>

<body class="wp-core-ui eaccounting">
<?php Template::get_template( 'global/header.php' ); ?>

<?php do_action( 'ever_accounting_before_body' ); ?>
<div class="ea-container">
	<?php do_action( 'ever_accounting_body' ); ?>
</div>
<?php do_action( 'ever_accounting_after_body' ); ?>

<?php Template::get_template( 'global/footer.php' ); ?>
</body>

</html>
