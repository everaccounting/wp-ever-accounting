<?php
/**
 * Displays company info.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/company-info.php.
 *
 * @var $invoice Invoice
 * @version 1.1.0
 */

use Ever_Accounting\Invoice;
use Ever_Accounting\Helpers\Misc;

defined( 'ABSPATH' ) || exit;

$company_details = array(
	'logo'       => ever_accounting_get_option( 'company_logo', ever_accounting_plugin_url( '/assets/dist/images/document-logo.png' ) ),
	'name'       => ever_accounting_get_option( 'company_name' ),
	'street'     => ever_accounting_get_option( 'company_address' ),
	'city'       => ever_accounting_get_option( 'company_city' ),
	'state'      => ever_accounting_get_option( 'company_state' ),
	'postcode'   => ever_accounting_get_option( 'company_postcode' ),
	'country'    => ever_accounting_get_option( 'company_country' ),
	'vat_number' => ever_accounting_get_option( 'company_vat_number' ),
);
$countries       = Misc::get_countries();
?>
<div class="ea-document__logo">
	<img src="<?php echo esc_url( $company_details['logo'] ); ?>" alt="<?php echo esc_html( $company_details['name'] ); ?>">
</div>
<address class="ea-document__company-info">
	<span class="ea-document__company-name"><?php echo esc_html( $company_details['name'] ); ?></span>
	<span class="ea-document__info-street"><?php echo esc_html( $company_details['street'] ); ?></span>
	<span class="ea-document__info-city"><?php echo esc_html( implode( ' ', array_filter( array( $company_details['city'], $company_details['state'], $company_details['postcode'] ) ) ) ); ?></span>
	<span class="ea-document__info-country"><?php echo isset( $countries[ $company_details['country'] ] ) ? esc_html( $countries[ $company_details['country'] ] ) : ''; ?></span>
	<?php if ( $company_details['vat_number'] ) : ?>
		<span class="ea-document__var-number"><?php _e( 'VAT Number', 'wp-ever-accounting' ); ?>: <span><?php echo esc_html( $company_details['vat_number'] ); ?></span></span>
	<?php endif; ?>
</address>
