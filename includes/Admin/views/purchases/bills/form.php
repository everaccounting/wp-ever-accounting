<?php
/**
 * Admin Bills Form.
 * Page: Expenses
 * Tab: Bills
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $bill \EverAccounting\Models\Bill Bill object.
 */

defined( 'ABSPATH' ) || exit;
// $state = wp_interactivity_state( 'eac/bill', $bill->to_array() );
?>
<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" data-wp-interactive="eac/bill">
	<span data-wp-text="name"></span>
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="eac-card__body grid--fields">
					<div class="eac-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $bill->name ); ?>"/>
					</div>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="eac-card__footer">
					<?php // if ( $bill->exists() ) : ?>
					<input type="hidden" name="id" value="<?php echo esc_attr( $bill->id ); ?>"/>
					<?php // endif; ?>
					<input type="hidden" name="action" value="eac_edit_bill"/>
					<?php wp_nonce_field( 'eac_edit_bill' ); ?>
					<?php // if ( $bill->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-expenses&tab=bills&id=' . $bill->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php // endif; ?>
					<?php // if ( $bill->exists() ) : ?>
					<button class="button button-primary"><?php esc_html_e( 'Update Bill', 'wp-ever-accounting' ); ?></button>
					<?php // else : ?>
					<button class="button button-primary eac-w-100"><?php esc_html_e( 'Add Bill', 'wp-ever-accounting' ); ?></button>
					<?php // endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->
</form>
<?php
