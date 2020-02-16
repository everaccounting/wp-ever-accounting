<?php
defined( 'ABSPATH' ) || exit();

// Get current tab/section.

$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'eaccounting_sections_' . $current_tab ) || has_action( 'eaccounting_settings_' . $current_tab ) || has_action( 'eaccounting_settings_tabs_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';

if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=eaccounting-settings' ) );
	exit;
}

?>
<div class="wrap eaccounting-settings">
	<?php do_action( 'eaccounting_before_settings_' . $current_tab ); ?>

    <form method="<?php echo esc_attr( apply_filters( 'eaccounting_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">

        <nav class="nav-tab-wrapper eaccounting-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $slug => $label ) {
				echo sprintf( '<a href="%1$s" class="nav-tab %2$s">%3$s</a>', esc_html( admin_url( 'admin.php?page=eaccounting-settings&tab=' . esc_attr( $slug ) ) ), ( $current_tab === $slug ? 'nav-tab-active' : '' ), esc_html( $label ) );
			}
			do_action( 'eaccounting_settings_tabs' );
			?>
        </nav>

        <h1 class="screen-reader-text"><?php echo esc_html( $current_tab_label ); ?></h1>

		<?php
		do_action( 'eaccounting_sections_' . $current_tab );
		//				self::show_messages();
		do_action( 'eaccounting_settings_' . $current_tab );
		?>

        <p class="submit">
			<?php //if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
                <button name="save" class="button-primary eaccounting-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'text-domain' ); ?>"><?php esc_html_e( 'Save changes', 'text-domain' ); ?></button>
			<?php //endif; ?>
			<?php wp_nonce_field( 'eaccounting-settings' ); ?>
        </p>

    </form>

	<?php do_action( 'eaccounting_after_settings_' . $current_tab ); ?>
</div>
