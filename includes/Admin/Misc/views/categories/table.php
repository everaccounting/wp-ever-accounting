<?php
/**
 * Admin List of Categories.
 * Page: Misc
 * Tab: Categories
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $category \EverAccounting\Models\Category Category object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Categories', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-misc&tab=categories&view=add' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<a href="<?php echo esc_attr( admin_url( 'admin.php?page=eac-tools' ) ); ?>" class="button button-small">
			<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
		</a>
		<?php if ( $list_table->get_request_search() ) : ?>
			<span class="subtitle"><?php echo esc_html( sprintf( /* translators: %s: Get requested search string */ __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $list_table->get_request_search() ) ) ); ?></span>
		<?php endif; ?>
	</h1>
	<hr class="wp-header-end">

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $list_table->views(); ?>
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $list_table->display(); ?>
		<input type="hidden" name="page" value="eac-misc"/>
		<input type="hidden" name="tab" value="categories"/>
	</form>
<?php
