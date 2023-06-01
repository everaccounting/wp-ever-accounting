<?php
/**
 * Index template.
 *
 * This template can be overridden by copying it to yourtheme/eac/accounting.php.
 *
 * @version 1.0.0
 * @package EverAccounting
 * @var  string $endpoint The endpoint being displayed.
 * @var  string $type The type of endpoint being displayed. (e.g. archive, single, page)
 */

defined( 'ABSPATH' ) || exit;

global $wp;
$vars = $wp->query_vars;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<link rel="profile" href="https://gmpg.org/xfn/11"/>
	<meta name="robots" content="noindex,nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_admin_css( 'install', true ); ?>
	<?php
	/**
	 * Fires in the <head> section of the accounting template.
	 *
	 * @param string $endpoint The endpoint being displayed.
	 * @param string $type The type of endpoint being displayed. (e.g. archive, single, page)
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_endpoint_head', $endpoint );
	?>
</head>
<body class="wp-core-ui">
<?php
/**
 * Fires before the accounting template content is output.
 *
 * @param string $endpoint The endpoint being displayed.
 * @param string $type The type of endpoint being displayed. (e.g. archive, single, page)
 *
 * @since 1.0.0
 */
do_action( 'ever_accounting_before_endpoint_content', $endpoint, $type );

$function = "eac_{$type}_{$endpoint}_endpoint_content";
$content  = wpautop( esc_html__( 'You do not have permission to view this page.', 'ever-accounting' ) );

if ( function_exists( $function ) ) {
	$output = call_user_func( $function, $vars );
	if ( ! empty( $output ) ) {
		$content = $output;
	}
}

echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

/**
 * Fires in the accounting template.
 *
 * @param array $vars The query vars.
 *
 * @since 1.0.0
 */
do_action( "ever_accounting_{$type}_{$endpoint}_endpoint_content", $vars );
/**
 * Fires after the accounting template.
 *
 * @param string $endpoint The endpoint being displayed.
 * @param string $type The type of endpoint being displayed. (e.g. archive, single, page)
 *
 * @since 1.0.0
 */
do_action( 'ever_accounting_after_endpoint_content', $endpoint, $type );

?>

</body>
<?php
/**
 * Fires in the <footer> section of the accounting template.
 *
 * @param string $endpoint The endpoint being displayed.
 * @param string $type The type of endpoint being displayed. (e.g. archive, single, page)
 *
 * @since 1.0.0
 */
do_action( 'ever_accounting_endpoint_footer', $endpoint, $type );
?>
</html>
