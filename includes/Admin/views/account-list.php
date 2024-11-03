<?php
/**
 * Admin View: Account List
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $account Account Account object.
 */

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

global $list_table;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Accounts', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-banking&tab=accounts&action=add' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
		</a>
		<?php if ( $list_table->get_request_search() ) : ?>
			<span class="subtitle"><?php echo esc_html( sprintf( /* translators: %s: Get requested search string */ __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $list_table->get_request_search() ) ) ); ?></span>
		<?php endif; ?>
	</h1>
	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : ''; ?>
		<?php $list_table->views(); ?>
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $list_table->display(); ?>
		<input type="hidden" name="page" value="eac-banking"/>
		<input type="hidden" name="tab" value="accounts"/>
		<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>"/>
	</form>
<?php
