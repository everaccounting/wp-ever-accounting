<?php
/**
 * Default header
 *
 * This template can be overridden by copying it to yourtheme/eac/header.php.
 *
 * @package EverAccounting\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="robots" content="noindex, nofollow"/>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="eac-page">
<?php
