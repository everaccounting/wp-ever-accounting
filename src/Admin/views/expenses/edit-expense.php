<?php
/**
 * View: Edit Expense
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $expense_id
 */

defined( 'ABSPATH' ) || exit();

$expense = new \EverAccounting\Models\Expense( $expense_id );
$title   = $expense->exists() ? __( 'Update Expense', 'wp-ever-accounting' ) : __( 'Add Expense', 'wp-ever-accounting' );
?>

<div class="eac-page__header">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title"><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div class="eac-page__header-col">
		<?php if ( $expense->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses&action=delete&expense_id=' . $expense->get_id() ), 'bulk-purchases' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
			<!--view-->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses&action=view&expense_id=' . $expense->get_id() ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'View Expense', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Expense', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-expense-form' ) ); ?>
	</div>
</div>
<?php
require __DIR__ . '/expense-form.php';
