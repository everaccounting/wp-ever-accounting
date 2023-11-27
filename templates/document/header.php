<?php
/**
 * Document Header Template
 *
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/eac/document/header.php
 *
 * HOWEVER, on occasion EverAccounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpeveraccounting.com/docs/
 * @package EverAccounting\Templates
 * @version 1.0.0
 */


defined( 'ABSPATH' ) || exit;

$company_name = get_option( 'eac_company_name', get_bloginfo( 'name' ) );
$logo         = get_option( 'eac_company_logo', '' );
?>
<div class="eac-document__header">
	<div class="eac-document__logo">
		<a href="https://pluginever.com">
			<?php if ( $logo ) : ?>
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
			<?php else : ?>
				<h2><?php echo esc_html( $company_name ); ?></h2>
			<?php endif; ?>
		</a>
	</div>
	<div class="eac-document__title">
		<div class="eac-document__title-text">Invoice</div>
		<div class="eac-document__title-meta">Inv-0001</div>
	</div>
</div>
