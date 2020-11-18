<?php
/**
 * Updates PHP versions to match those in package.json before start or build.
 *
 * @package WP Ever Accounting
 */

$package_json = file_get_contents('./package.json');
$package      = json_decode($package_json);

function replace_version( $filename, $package_json )
{
    $lines = array();
    $file  = file($filename);

    foreach ( $file as $line ) {
        if (stripos($line, ' * Version: ') !== false ) {
            $line = " * Version: {$package_json->version}\n";
        }
        if (stripos($line, ">define( 'EACCOUNTING_VERSION',") !== false ) {
            $line = "\t\t\$this->define( 'EACCOUNTING_VERSION', '{$package_json->version}' );\n";
        }
        $lines[] = $line;
    }
    file_put_contents($filename, $lines);
}

replace_version('wp-ever-accounting.php', $package);
