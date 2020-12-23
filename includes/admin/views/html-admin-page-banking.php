<?php
/**
 * Admin View: Page - Banking
 *
 * @var array  $tabs
 * @var string $current_tab
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap eaccounting ea-banking">
	<nav class="nav-tab-wrapper ea-nav-tab-wrapper">
		<?php
		foreach ( $tabs as $name => $label ) {
			echo '<a href="' . admin_url( 'admin.php?page=ea-banking&tab=' . $name ) . '" class="nav-tab ';
			if ( $current_tab === $name ) {
				echo 'nav-tab-active';
			}
			echo '">' . esc_html( $label ) . '</a>';
		}
		?>
	</nav>
	<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
	<div class="ea-admin-page">
		<?php
		switch ( $current_tab ) {
			case 'accounts':
				EAccounting_Admin_Banking::accounts();
				break;
			case 'transactions':
				EAccounting_Admin_Banking::transactions();
				break;
			case 'transfers':
				EAccounting_Admin_Banking::transfers();
				break;
			default:
				if ( array_key_exists( $current_tab, $tabs ) && has_action( 'eaccounting_banking_tab_' . $current_tab ) ) {
					do_action( 'eaccounting_banking_tab_' . $current_tab );
				}
				break;
		}
		?>
	</div>
</div>
