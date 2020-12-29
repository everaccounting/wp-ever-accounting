<?php
/**
 * Invoice header.
 *
 * @var $invoice \EverAccounting\Models\Invoice
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;
$company_logo = eaccounting()->settings->get( 'company_logo' );
$site_name    = wp_parse_url( site_url() )['host'];
?>
<div class="ea-document__header">
	<div class="ea-document__logo">
		<?php if ( ! empty( $company_logo ) ) : ?>
			<img src="<?php echo esc_url( $company_logo ); ?>" alt="<?php echo esc_attr( $site_name ); ?>">
		<?php else : ?>
			<h2><?php echo esc_html( $site_name ); ?></h2>
		<?php endif; ?>
	</div>

	<div class="ea-document__title"><?php _e( 'Invoice', '' ); ?></div>
</div>
