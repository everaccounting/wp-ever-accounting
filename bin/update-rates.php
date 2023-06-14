<?php
/**
 * What is this file for?
 *
 * The purpose of this file is to update the currency conversion rates of i18n/currencies.php
 * This script Get the conversion rates from https://www.floatrates.com/daily/usd.json
 * Then loop through the currencies in i18n/currencies.php and update the conversion rates
 * then fix the format of the file to be a valid php array
 */

// Get the conversion rates from https://www.floatrates.com/daily/usd.json
$rates = json_decode( file_get_contents( 'https://www.floatrates.com/daily/usd.json' ), true );

// Loop through the currencies in i18n/currencies.php and update the conversion rates
$currencies = include 'i18n/currencies.php';
foreach ( $currencies as $code => $currency ) {
	if ( isset( $rates[ $code ] ) ) {
		$currencies[ $code ]['rate'] = round( $rates[ $code ]['rate'], 8 );
	}
}

// Fix the format of the file to be a valid php array
$php = "<?php\n\n defined( 'ABSPATH' ) || exit;\n\n return array(\n";
foreach ( $currencies as $code => $currency ) {
	$php .= "\t'{$code}' => array(\n";
	foreach ( $currency as $key => $value ) {
		$php .= "\t\t'{$key}' => '{$value}',\n";
	}
	$php .= "\t),\n";
}

// Save the file
file_put_contents( 'i18n/currencies-bk1.php', $php );

