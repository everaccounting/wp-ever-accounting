<?php
/**
 * View: Edit Category
 * Page: Settings
 * Tab: Categories
 *
 * @since       1.0.2
 *
 * @subpackage  Admin/View/Settings
 * @package     EverAccounting
 * @var int $category_id
 */

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit();

$category = new Category( $category_id );
$title    = $category->exists() ? __( 'Update Category', 'wp-ever-accounting' ) : __( 'Add Category', 'wp-ever-accounting' );
?>

	<div class="eac-section-header">
		<div>
			<h2 class="eac-section-header__title"><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( remove_query_arg( [ 'action', 'category_id' ] ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $category->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-settings&tab=categories&action=delete&category_id=' . $category->get_id() ), 'bulk-categories' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Category', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-category-form' ) ); ?>
		</div>
	</div>
<?php
require __DIR__ . '/category-form.php';