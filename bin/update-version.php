<?php
/**
 * Updates PHP versions to match those in package.json before start or build.
 *
 * @package EAccounting
 */

$package_json = file_get_contents( 'package.json' );
$package      = json_decode( $package_json );

/**
 * @param $filename
 * @param $package_json
 */
function replace_version( $filename, $package_json ) {
	$lines = array();
	$file  = file( $filename );

	foreach ( $file as $line ) {
		if ( stripos( $line, ' * Version: ' ) !== false ) {
			$line = " * Version: {$package_json->version}\n";
		}
		if ( stripos( $line, "const VERSION =" ) !== false ) {
			$line = "\tconst VERSION = '{$package_json->version}';\n";
		}
		if ( stripos( $line, 'Stable tag: ' ) !== false ) {
			$line = "Stable tag: {$package_json->version}\n";
		}
		if ( stripos( $line, '"version":' ) !== false ) {
			$line = "\t\"version\": \"{$package_json->version}\",\n";
		}
		$lines[] = $line;
	}
	file_put_contents( $filename, $lines );
}

replace_version( 'wp-ever-accounting.php', $package );
replace_version( 'readme.txt', $package );
replace_version( 'composer.json', $package );
