<?php
/**
 * View: Edit Vendor
 * Page: Settings
 * Tab: Categories
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Settings
 * @package     EverAccounting
 * @var int $vendor_id
 */

use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit();

$vendor = new Vendor( $vendor_id );
$title  = $vendor->exists() ? __( 'Update Vendor', 'wp-ever-accounting' ) : __( 'Add Vendor', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchase&tab=vendors' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $vendor->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-purchase&tab=vendors&action=delete&vendor_id=' . $vendor->get_id() ), 'bulk-vendor' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Vendor', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-vendor-form' ) ); ?>
		</div>
	</div>

<?php
require __DIR__ . '/vendor-form.php';
