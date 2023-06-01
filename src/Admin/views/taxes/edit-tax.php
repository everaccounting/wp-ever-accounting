<?php
/**
 * View: Edit Tax Rate
 * Page: Settings
 * Tab: Tax
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Settings
 * @package     EverAccounting
 * @var int $tax_id
 */

use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit();

$tax   = new Tax( $tax_id );
$title = $tax->exists() ? __( 'Update Rate', 'wp-ever-accounting' ) : __( 'Add Rate', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-settings&tab=tax&section=taxes' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $tax->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-settings&tab=tax&section=taxes&action=delete&tax_id=' . $tax->get_id() ), 'bulk-tax' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Rate', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-tax-form' ) ); ?>
		</div>
	</div>

<?php
require __DIR__ . '/tax-form.php';
