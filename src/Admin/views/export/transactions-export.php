<?php
/**
 * View: Transactions export form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Export
 */

defined( 'ABSPATH' ) || exit();

?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="get">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php echo esc_html__( 'Payments export' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<?php
				eac_input_field(array(
					'type' => 'select',
					'pay'
				))
			?>
		</div>
	</div>
</form>

