<?php
/**
 * Admin View: Settings
 *
 * @param string $current_tab Current tab.
 * @param array $tabs Tabs.
 *
 * @since   1.0.0
 * @package EverAccounting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$page              = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'ever_accounting_sections_' . $current_tab ) || has_action( 'ever_accounting_settings_' . $current_tab ) || has_action( 'ever_accounting_settings_tabs_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';

if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=ea-settings' ) );
	exit;
}
?>
<div class="wrap ever-accounting">
	<?php do_action( 'ever_accounting_before_settings_' . $current_tab ); ?>
	<form method="<?php echo esc_attr( apply_filters( 'ever_accounting_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper ea-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $slug => $label ) {
				echo sprintf(
						'<a href="%s" class="nav-tab %s">%s</a>',
						add_query_arg( array( 'page' => $page, 'tab' => $slug ), admin_url( 'admin.php' ) ),
						( $current_tab === $slug ? 'nav-tab-active' : '' ),
						esc_html( $label )
				);
			}
			?>
			<?php do_action( 'ever_accounting_settings_tabs' ); ?>
		</nav>
		<h1 class="screen-reader-text"><?php echo esc_html( $current_tab_label ); ?></h1>
		<?php do_action( 'ever_accounting_sections_' . $current_tab ); ?>
		<?php do_action( 'ever_accounting_settings_' . $current_tab ); ?>
		<?php if ( apply_filters( 'ever_accounting_settings_show_save_button', empty( $GLOBALS['hide_save_button'] ) ) ) : ?>
			<p class="submit">
				<button name="save" class="button-primary ea-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'wp-ever-accounting' ); ?>">
					<?php esc_html_e( 'Save changes', 'wp-ever-accounting' ); ?>
				</button>
			</p>
		<?php endif; ?>
		<?php wp_nonce_field( 'ever-accounting-settings' ); ?>
	</form>
	<?php do_action( 'ever_accounting_after_settings_' . $current_tab ); ?>
</div>
