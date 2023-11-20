<?php
/**
 * View: View Expense
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $expense_id
 */

defined( 'ABSPATH' ) || exit();
$expense = new \EverAccounting\Models\Expense( $expense_id );
$actions = array(
	array(
		'url'  => admin_url( 'admin.php?page=eac-purchases&tab=expenses&action=edit&expense_id=' . $expense->get_id() ),
		'text' => __( 'Edit', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses&action=delete&expense_id=' . $expense->get_id() ), 'eac_delete_expense' ),
		'text' => __( 'Delete', 'wp-ever-accounting' ),
	),
);
$actions = apply_filters( 'eac_expense_actions', $actions, $expense_id );
?>
<div class="eac-section-header margin-bottom-4">
	<div>
		<h2>
			<?php echo esc_html( $expense->get_number() ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses' ) ); ?>"><span
				class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php
		/**
		 * Action before expense actions.
		 *
		 * @param int $expense_id Expense ID.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eac_expense_before_actions', $expense_id );
		?>
		<a href="<?php echo esc_url( eac_action_url( 'action=send_expense_receipt&id=' . $expense->get_id(), false ) ); ?>"
		   class="button button-primary">
			<?php esc_html_e( 'Send Receipt', 'wp-ever-accounting' ); ?>
		</a>
		<?php eac_dropdown_menu( $actions ); ?>
		<?php
		/**
		 * Action after expense actions.
		 *
		 * @param int $expense_id Expense ID.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eac_expense_after_actions', $expense_id );
		?>
	</div>
</div>

<div class="eac-columns">
	<div class="eac-col-9">
		<?php eac_display_expense( $expense_id ); ?>
	</div>
	<div class="eac-col-3">
		<div class="eac-card margin-top-0" style="margin-top: 0 !important;">
			<div class="eac-card__header">
				<div class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></div>
			</div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perspiciatis, sed.
			</div>
		</div>
	</div>
</div>
