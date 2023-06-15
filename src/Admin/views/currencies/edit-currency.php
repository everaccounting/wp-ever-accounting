<?php
/**
 * View: Edit Currency
 * Page: Settings
 * Tab: Currencies
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Currencies
 * @package     EverAccounting
 *
 * @var int $currency_id Currency ID.
 */

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit();

$currency = new Currency( $currency_id );
$title    = $currency->exists() ? __( 'Update Currency', 'wp-ever-accounting' ) : __( 'Add Currency', 'wp-ever-accounting' );
?>

<div class="eac-section-header">
	<div>
		<h2><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-settings&section=currencies' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php submit_button( __( 'Save Currency', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-currency-form' ) ); ?>
	</div>
</div>
<?php
require __DIR__ . '/currency-form.php';
