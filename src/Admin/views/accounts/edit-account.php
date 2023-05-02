<?php
/**
 * Admin Account Edit Page.
 * Page: Banking
 * Tab: Accounts
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Accounts
 * @package     EverAccounting
 * @var int $account_id
 */

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit();

$account = new Account( $account_id );
$title   = $account->exists() ? __( 'Update Account', 'wp-ever-accounting' ) : __( 'Add Account', 'wp-ever-accounting' );
?>

<div class="eac-page__header">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title"><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div class="eac-page__header-col">
		<?php if ( $account->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&delete=' . $account->get_id() ), 'bulk-accounts' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Account', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-account-form' ) ); ?>
	</div>
</div>

<?php
require __DIR__ . '/account-form.php';
