<?php
/**
 * Admin List of Customers.
 * Page: Sales
 * Tab: Customers
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $customer \EverAccounting\Models\Customer Customer object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Customers', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="page-title-action">
			<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
		</a>
		<?php if ( $this->list_table->get_request_search() ) : ?>
			<span class="subtitle"><?php echo esc_html( sprintf( /* translators: %s: Get requested search string */ __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $this->list_table->get_request_search() ) ) ); ?></span>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $this->list_table->views(); ?>
		<?php $this->list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $this->list_table->display(); ?>
		<input type="hidden" name="page" value="eac-sales"/>
		<input type="hidden" name="tab" value="customers"/>
	</form>
<?php
