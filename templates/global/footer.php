<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/footer.php.
 *
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

?>
<?php do_action( 'eaccounting_after_main_content' ); ?>
</div>
</div>
<?php
do_action( 'eaccounting_before_footer' );
do_action( 'eaccounting_must_footer' );
do_action( 'eaccounting_after_footer' );
?>

</body>
</html>
