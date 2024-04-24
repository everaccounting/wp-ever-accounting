<?php
/**
 * Admin Customers Form.
 * Page: Sales
 * Tab: Customers
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $customer \EverAccounting\Models\Customer Customer object.
 */

defined( 'ABSPATH' ) || exit;
// $state = wp_interactivity_state( 'eac/customer', $customer->to_array() );
?>
<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" data-wp-interactive="eac/customer">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">
					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $customer->name ); ?>"/>
					</div>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__footer">
					<?php // if ( $customer->exists() ) : ?>
					<input type="hidden" name="id" value="<?php echo esc_attr( $customer->id ); ?>"/>
					<?php // endif; ?>
					<input type="hidden" name="action" value="eac_edit_customer"/>
					<?php wp_nonce_field( 'eac_edit_customer' ); ?>
					<?php // if ( $customer->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=customers&id=' . $customer->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php // endif; ?>
					<?php // if ( $customer->exists() ) : ?>
					<button class="button button-primary"><?php esc_html_e( 'Update Customer', 'wp-ever-accounting' ); ?></button>
					<?php // else : ?>
					<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?></button>
					<?php // endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->

	</div><!-- .bkit-poststuff -->
</form>
<?php
