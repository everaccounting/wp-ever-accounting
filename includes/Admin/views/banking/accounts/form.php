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

						<?php
						eac_form_group(
							array(
								'label'       => __( 'Name', 'wp-ever-accounting' ),
								'type'        => 'text',
								'name'        => 'name',
								'value'       => $account->name,
								'placeholder' => __( 'XYZ Saving Account', 'wp-ever-accounting' ),
								'required'    => true,
							)
						);

						eac_form_group(
							array(
								'label'       => __( 'Number', 'wp-ever-accounting' ),
								'type'        => 'text',
								'name'        => 'number',
								'value'       => $account->number,
								'placeholder' => __( '1234567890', 'wp-ever-accounting' ),
								'required'    => true,
							)
						);

						eac_form_group(
							array(
								'label'       => __( 'Type', 'wp-ever-accounting' ),
								'type'        => 'select',
								'name'        => 'type',
								'value'       => $account->type,
								'options'     => \EverAccounting\Models\Account::get_types(),
								'placeholder' => __( 'Select Type', 'wp-ever-accounting' ),
								'required'    => true,
							)
						);

						eac_form_group(
							array(
								'label'        => __( 'Currency', 'wp-ever-accounting' ),
								'type'         => 'select',
								'name'         => 'currency_code',
								'value'        => $account->currency_code,
								'options'      => \EverAccounting\Models\Currency::results( array( 'status' => 'active' ) ),
								'option_label' => 'formatted_name',
								'option_value' => 'code',
								'placeholder'  => __( 'Select Currency', 'wp-ever-accounting' ),
								'required'     => true,
							)
						);
						eac_form_group(
							array(
								'label'       => __( 'Bank Name', 'wp-ever-accounting' ),
								'type'        => 'text',
								'name'        => 'bank_name',
								'value'       => $account->bank_name,
								'placeholder' => __( 'XYZ Bank', 'wp-ever-accounting' ),
							)
						);
						eac_form_group(
							array(
								'label'       => __( 'Bank Phone', 'wp-ever-accounting' ),
								'type'        => 'text',
								'name'        => 'bank_phone',
								'value'       => $account->bank_phone,
								'placeholder' => __( '+1234567890', 'wp-ever-accounting' ),
							)
						);
						eac_form_group(
							array(
								'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
								'type'          => 'textarea',
								'name'          => 'bank_address',
								'value'         => $account->bank_address,
								'placeholder'   => __( '123, XYZ Street, City, Country', 'wp-ever-accounting' ),
								'wrapper_class' => 'full--width',
							)
						);
						?>
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
