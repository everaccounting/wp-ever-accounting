<?php
/**
 * View: Edit Customer
 * Page: Settings
 * Tab: Categories
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Settings
 * @package     EverAccounting
 * @var int $customer_id
 */

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit();

$customer = new Customer( $customer_id );
$title    = $customer->exists() ? __( 'Update Customer', 'wp-ever-accounting' ) : __( 'Add Customer', 'wp-ever-accounting' );
?>

	<div class="eac-page__header">
		<div class="eac-page__header-col">
			<h2 class="eac-page__title"><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div class="eac-page__header-col">
			<?php if ( $customer->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=customers&action=delete&customer_id=' . $customer->get_id() ), 'bulk-customer' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Customer', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-customer-form' ) ); ?>
		</div>
	</div>

<?php
require __DIR__ . '/customer-form.php';
