<?php
/**
 * Currency view.
 *
 * @package EverAccounting\Admin\Views\Currencies
 * @version 1.0.0
 * @var $currency \EverAccounting\Models\Currency Currency object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-currency-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__body grid--fields">

					<div class="bkit-form-group">
						<label for="code">
							<?php esc_html_e( 'Code', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="code" id="code" value="<?php echo esc_attr( $currency->code ); ?>" readonly/>
					</div>

					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $currency->name ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="symbol">
							<?php esc_html_e( 'Symbol', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="symbol" id="symbol" value="<?php echo esc_attr( $currency->symbol ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="exchange_rate">
							<?php esc_html_e( 'Exchange Rate', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="exchange_rate" id="exchange_rate" value="<?php echo esc_attr( $currency->exchange_rate ); ?>"/>
					</div>
					<div class="bkit-form-group">
						<label for="thousand_separator">
							<?php esc_html_e( 'Thousand Separator', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="thousand_separator" id="thousand_separator" value="<?php echo esc_attr( $currency->thousand_separator ); ?>"
							   <?php echo eac_get_base_currency() == $currency->code ? 'readonly' : ''; ?>/>
					</div>

					<div class="bkit-form-group">
						<label for="decimal_separator">
							<?php esc_html_e( 'Decimal Separator', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="decimal_separator" id="decimal_separator" value="<?php echo esc_attr( $currency->decimal_separator ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="precision">
							<?php esc_html_e( 'Number of Decimals', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="number" name="precision" id="precision" value="<?php echo esc_attr( $currency->precision ); ?>"/>
					</div>

					<div class="bkit-form-group">
						<label for="position">
							<?php esc_html_e( 'Symbol Position', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<select name="position" id="position" required="required">
							<option value=""><?php esc_html_e( 'Select an option…', 'wp-ever-accounting' ); ?></option>
							<option value="before" <?php selected( 'before', $currency->position ); ?>><?php esc_html_e( 'Before amount', 'wp-ever-accounting' ); ?></option>
							<option value="after" <?php selected( 'after', $currency->position ); ?>><?php esc_html_e( 'After amount', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
				</div>
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
							<option value="active" <?php selected( 'active', $currency->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
							<option value="inactive" <?php selected( 'inactive', $currency->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
				</div>

				<div class="bkit-card__footer">
					<input type="hidden" name="id" value="<?php echo esc_attr( $currency->id ); ?>"/>
					<input type="hidden" name="action" value="eac_edit_currency"/>
					<?php wp_nonce_field( 'eac_edit_currency' ); ?>
					<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Update', 'wp-ever-accounting' ); ?></button>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
