<?php
/**
 * View: Edit Income
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $income_id
 */

defined( 'ABSPATH' ) || exit();

$income = new \EverAccounting\Models\Income( $income_id );
$title  = $income->exists() ? __( 'Update Income', 'wp-ever-accounting' ) : __( 'Add Income', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=incomes' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $income->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=incomes&action=delete&income_id=' . $income->get_id() ), 'bulk-items' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Income', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-income-form' ) ); ?>
		</div>
	</div>
<?php
require __DIR__ . '/income-form.php';
