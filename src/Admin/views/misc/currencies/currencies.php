<?php
/**
 * Currencies view.
 *
 * @since 1.2.0
 * @package EverAccounting
 * @subpackage Admin/Views/Banking/Currencies
 */

defined( 'ABSPATH' ) || exit;
?>
	<div class="eac-section-header">
		<div>
			<h2 class="eac-section-header__title">
				<?php esc_html_e( 'Currencies', 'wp-ever-accounting' ); ?>
			</h2>
			<?php if ( $this->list_table->get_request_search() ) : ?>
				<span class="subtitle"><?php echo esc_html( sprintf( __( 'Search results for "%s"', 'wp-ever-accounting' ), esc_html( $this->list_table->get_request_search() ) ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<hr class="wp-header-end">

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $this->list_table->views(); ?>
		<?php $this->list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $this->list_table->display(); ?>
		<input type="hidden" name="page" value="eac-misc"/>
		<input type="hidden" name="tab" value="currencies"/>
	</form>
<?php
