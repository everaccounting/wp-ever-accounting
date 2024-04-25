<?php
/**
 * Admin Accounts Form.
 * Page: Banking
 * Tab: Accounts
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $account \EverAccounting\Models\Account Account object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<form id="eac-account-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<span data-wp-text="name"></span>
		<div class="bkit-poststuff">
			<div class="column-1">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Account details', 'wp-ever-accounting' ); ?></h2>
					</div>

					<div class="bkit-card__body grid--fields">
						<div class="bkit-form-group">
							<label for="name">
								<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
								<abbr title="required"></abbr>
							</label>
							<input type="text" name="name" id="name" placeholder="<?php esc_attr_e( 'Saving Account', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $account->name ); ?>" required/>
						</div>

						<div class="bkit-form-group">
							<label for="number">
								<?php esc_html_e( 'Number', 'wp-ever-accounting' ); ?>
								<abbr title="required"></abbr>
							</label>
							<input type="text" name="number" id="number" placeholder="<?php esc_attr_e( '1234567890', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $account->number ); ?>" required/>
						</div>
						<div class="bkit-form-group">
							<label for="type">
								<?php esc_html_e( 'Type', 'wp-ever-accounting' ); ?>
								<abbr title="required"></abbr>
							</label>
							<select name="type" id="type" required>
								<option value=""><?php esc_html_e( 'Select Type', 'wp-ever-accounting' ); ?></option>
								<?php foreach ( eac_get_account_types() as $key => $value ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $account->type ); ?>><?php echo esc_html( $value ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="bkit-form-group">
							<label for="currency_code">
								<?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?>
								<abbr title="required"></abbr>
							</label>
							<select name="currency_code" id="currency_code" required>
								<option value=""><?php esc_html_e( 'Select Currency', 'wp-ever-accounting' ); ?></option>
								<?php foreach ( eac_get_currencies( array( 'limit' => - 1, 'status' => 'active' ) ) as $currency ) : ?>
									<option value="<?php echo esc_attr( $currency->code ); ?>" <?php selected( $currency->code, $account->currency_code ); ?>><?php echo esc_html( $currency->formatted_name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="bkit-form-group">
							<label for="opening_balance">
								<?php esc_html_e( 'Opening Balance', 'wp-ever-accounting' ); ?>
							</label>
							<input type="number" name="opening_balance" id="opening_balance" placeholder="<?php esc_attr_e( '0.00', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $account->opening_balance ); ?>"/>
						</div>

						<div class="bkit-form-group">
							<label for="bank_name">
								<?php esc_html_e( 'Bank Name', 'wp-ever-accounting' ); ?>
							</label>
							<input type="text" name="bank_name" id="bank_name" placeholder="<?php esc_attr_e( 'XYZ Bank', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $account->bank_name ); ?>"/>
						</div>

						<div class="bkit-form-group">
							<label for="bank_phone">
								<?php esc_html_e( 'Bank Phone', 'wp-ever-accounting' ); ?>
							</label>
							<input type="text" name="bank_phone" id="bank_phone" placeholder="<?php esc_attr_e( '+1234567890', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $account->bank_phone ); ?>"/>
						</div>

						<div class="bkit-form-group is--full">
							<label for="bank_address">
								<?php esc_html_e( 'Bank Address', 'wp-ever-accounting' ); ?>
							</label>
							<textarea name="bank_address" id="bank_address" placeholder="<?php esc_attr_e( '123, XYZ Street, City, Country', 'wp-ever-accounting' ); ?>"><?php echo esc_textarea( $account->bank_address ); ?></textarea>
						</div>
					</div><!-- .bkit-card__body -->
				</div>
			</div><!-- .column-1 -->

			<div class="column-2">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="bkit-card__body">
						<div class="bkit-form-group">
							<label for="status">
								<?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?>
							</label>
							<select name="status" id="status">
								<option value="active" <?php selected( 'active', $account->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
								<option value="inactive" <?php selected( 'inactive', $account->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
							</select>
						</div>
					</div>
					<div class="bkit-card__footer">
						<?php if ( $account->exists() ) : ?>
							<input type="hidden" name="id" value="<?php echo esc_attr( $account->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_account"/>
						<?php wp_nonce_field( 'eac_edit_account' ); ?>
						<?php if ( $account->exists() ) : ?>
							<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-banking&tab=accounts&id=' . $account->id ) ), 'bulk-accounts' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $account->exists() ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Update Account', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
							<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Account', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- .column-2 -->

		</div><!-- .bkit-poststuff -->
	</form>
<?php
