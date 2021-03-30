<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/form-login.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_user_logged_in() ) {
	return;
}

?>
<form class="eaccounting-form eaccounting-form-login login" method="post" <?php echo ( $hidden ) ? 'style="display:none;"' : ''; ?>>

	<?php do_action( 'eaccounting_login_form_start' ); ?>

	<?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // @codingStandardsIgnoreLine ?>

	<p class="form-row form-row-first">
		<label for="username"><?php esc_html_e( 'Username or email', 'wp-ever-accounting' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="input-text" name="username" id="username" autocomplete="username" />
	</p>
	<p class="form-row form-row-last">
		<label for="password"><?php esc_html_e( 'Password', 'wp-ever-accounting' ); ?>&nbsp;<span class="required">*</span></label>
		<input class="input-text" type="password" name="password" id="password" autocomplete="current-password" />
	</p>
	<div class="clear"></div>

	<?php do_action( 'eaccounting_login_form' ); ?>

	<p class="form-row">
		<label class="eaccounting-form__label eaccounting-form__label-for-checkbox eaccounting-form-login__rememberme">
			<input class="eaccounting-form__input eaccounting-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'wp-ever-accounting' ); ?></span>
		</label>
		<?php wp_nonce_field( 'eaccounting-login', 'eaccounting-login-nonce' ); ?>
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
		<button type="submit" class="eaccounting-button button eaccounting-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'wp-ever-accounting' ); ?>"><?php esc_html_e( 'Login', 'wp-ever-accounting' ); ?></button>
	</p>
	<p class="lost_password">
		<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'wp-ever-accounting' ); ?></a>
	</p>

	<div class="clear"></div>

	<?php do_action( 'eaccounting_login_form_end' ); ?>

</form>
