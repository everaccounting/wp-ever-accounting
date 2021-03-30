<?php
/**
 * EverAccounting Template functions.
 *
 * Functions related to templates.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get template part.
 *
 * @param mixed $slug Template slug.
 * @param string $name Template name (default: '').
 */
function eaccounting_get_template_part( $slug, $name = null ) {
	$template = false;
	if ( $name ) {
		$template = locate_template(
				array(
						"{$slug}-{$name}.php",
						eaccounting()->template_path() . "{$slug}-{$name}.php",
				)
		);

		if ( ! $template ) {
			$fallback = eaccounting()->plugin_path() . "/templates/{$slug}-{$name}.php";
			$template = file_exists( $fallback ) ? $fallback : '';
		}
	}

	if ( ! $template ) {
		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/eaccounting/slug.php.
		$template = locate_template(
				array(
						"{$slug}.php",
						eaccounting()->template_path() . "{$slug}.php",
				)
		);
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'eaccounting_get_template_part', $template, $slug, $name );
	var_dump( $template );
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string
 */
function eaccounting_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = eaccounting()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = eaccounting()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
			array(
					trailingslashit( $template_path ) . $template_name,
					$template_name,
			)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'eaccounting_locate_template', $template, $template_name, $template_path );
}


/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 */
function eaccounting_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$template = eaccounting_locate_template( $template_name, $template_path, $default_path );

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'eaccounting_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			$filter_template = $template;
		}
	}

	$action_args = array(
			'template_name' => $template_name,
			'template_path' => $template_path,
			'located'       => $template,
			'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	do_action( 'eaccounting_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'eaccounting_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Like eaccounting_get_template, but returns the HTML instead of outputting.
 *
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 *
 * @since 1.0.2
 * @return string
 * @see   eaccounting_get_template
 *
 */
function eaccounting_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	eaccounting_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}

/**
 * Get admin view.
 *
 * since 1.0.2
 *
 * @param       $template_name
 * @param array $args
 * @param null $path
 */
function eaccounting_get_admin_template( $template_name, $args = array(), $path = null ) {

	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	$template_name = str_replace( '.php', '', $template_name );
	if ( is_null( $path ) ) {
		$path = EACCOUNTING_ABSPATH . '/includes/admin/views/';
	}
	$template = apply_filters( 'eaccounting_admin_template', $template_name );
	$file     = $path . $template . '.php';
	if ( ! file_exists( $file ) ) {
		/* Translators: %s file name */
		eaccounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Admin template %s does not exist', 'wp-ever-accounting' ), $file ), null );

		return;
	}
	include $file;
}

/**
 * Render admin template.
 *
 * @param array $args
 *
 * @param       $template_name
 *
 * @since 1.0.0
 *
 * @return string
 */
function eaccounting_get_admin_template_html( $template_name, $args = array() ) {
	ob_start();

	eaccounting_get_admin_template( $template_name, $args );

	return ob_get_clean();
}

/**
 * Return frontend url.
 *
 * @param string $path
 *
 * @return string
 */
function eaccounting_get_url( $path = '' ) {
	return site_url( trailingslashit( eaccounting_get_base_url() ) . ltrim( $path, '/' ) );
}

/**
 * Get base slug.
 *
 * @since 1.1.0
 */
function eaccounting_get_base_url() {
	return apply_filters( 'eaccounting_base_url', 'eaccounting' );
}

/**
 * Output the start of the page wrapper.
 */
function eaccounting_page_header() {
	eaccounting_get_template( 'global/header.php' );
}

/**
 * Output the end of the page wrapper.
 */
function eaccounting_page_footer() {
	eaccounting_get_template( 'global/footer.php' );
}

/**
 * Restricted content.
 */
function eaccounting_page_content_unauthorized() {
	eaccounting_get_template_part( 'content', 'unauthorized' );
}

/**
 * Invoice page content.
 */
function eaccounting_page_content_invoices() {
	$singular = get_query_var( 'singular' );
	$id       = get_query_var( 'id' );
	$key      = filter_input( INPUT_GET, 'key', FILTER_SANITIZE_STRING );

	if ( empty( $singular ) || empty( $id ) || empty( $key ) ) {
		eaccounting_page_content_unauthorized();

		return;
	}

	$invoice = eaccounting_get_invoice( $id );
	if ( empty( $invoice ) || ! $invoice->is_key_valid( $key ) ) {
		eaccounting_page_content_unauthorized();

		return;
	}

	eaccounting_get_template(
			'single-invoice.php',
			array(
					'invoice' => $invoice,
					'key'     => $key,
			)
	);
}

/**
 * Output invoice page header.
 *
 * @param \EverAccounting\Models\Invoice $invoice Invoice object.
 */
function eaccounting_page_invoice_header( $invoice ) {
	if ( ! is_object( $invoice ) || ! $invoice->exists() ) {
		return;
	}
	eaccounting_get_template(
			'invoice/invoice-header.php',
			array(
					'invoice' => $invoice,
			)
	);
}

/**
 * Output the Payment form.
 *
 * @param \EverAccounting\Models\Invoice $invoice Invoice object.
 */
function eaccounting_checkout_form( $invoice ) {
	$gateways = eaccounting()->gateways->get_active_gateways();

	if ( ! $invoice->needs_payment() || empty( $gateways ) ) {
		return;
	}
	$gateway = eaccounting_get_option( 'default_gateway' );
	if ( empty( $gateway ) || empty( $gateways[ $gateway ] ) ) {
		$gateway = current( $gateways )->id;
	}

	eaccounting_get_template(
			'checkout-form.php',
			array(
					'invoice'  => $invoice,
					'gateways' => $gateways,
					'selected' => $gateway,
			)
	);
}

/**
 * @param $invoice
 */
function eaccounting_checkout_button( $invoice ) {
	$button_text = apply_filters( 'eaccounting_checkout_button_text', __( 'Pay Now', 'wp-ever-accounting' ), $invoice );
	?>
	<?php do_action( 'eaccounting_checkout_form_before_submit', $invoice ); ?>
	<?php if ( is_user_logged_in() ) { ?>
		<input type="hidden" name="user_id" value="<?php echo esc_attr( get_current_user_id() ); ?>"/>
	<?php } ?>
	<input type="hidden" name="invoice_id" value="<?php echo esc_attr( $invoice->get_id() ); ?>">
	<input type="hidden" name="payment_key" value="<?php echo esc_attr( $invoice->get_key() ); ?>">
	<input type="hidden" name="action" value="eaccounting_process_checkout">
	<?php wp_nonce_field( 'eaccounting_process_checkout', 'eaccounting_process_checkout_nonce' ); ?>
	<input type="submit" class="button button-primary" value="<?php echo esc_html( $button_text ); ?>"/>
	<?php do_action( 'eaccounting_checkout_form_after_submit', $invoice ); ?>
	<?php
}

/**
 * @param \EverAccounting\Models\Invoice $invoice
 */
function eaccounting_checkout_total_amount( $invoice ) {
	?>
	<p id="eaccounting_checkout_total">
		<strong><?php esc_html_e( 'Payable Total:', 'wp-ever-accounting' ); ?></strong>
		<span><?php echo eaccounting_price( $invoice->get_total_due(), $invoice->get_currency_code() ); ?></span>
	</p>
	<?php
}


function eaccounting_page_content_checkout() {
	$nonce          = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
	$invoice_id     = (int) filter_input( INPUT_GET, 'invoice_id', FILTER_SANITIZE_NUMBER_INT );
	$payment_key    = filter_input( INPUT_GET, 'payment_key', FILTER_SANITIZE_STRING );
	$payment_method = filter_input( INPUT_GET, 'payment_method', FILTER_SANITIZE_STRING );
	if ( ! wp_verify_nonce( $nonce, 'eaccounting_invoice_payment' ) || empty( $invoice_id ) || empty( $payment_key ) || empty( $payment_method ) ) {
		eaccounting_redirect( eaccounting_get_url( 'unauthorized' ) );
	}

	$invoice = eaccounting_get_invoice( $invoice_id );
	if ( empty( $invoice ) || ! $invoice->exists() || $payment_key !== $invoice->get_key() ) {
		eaccounting_redirect( eaccounting_get_url( 'unauthorized' ) );
	}

	if ( ! $invoice->needs_payment() ) {
		eaccounting_redirect( $invoice->get_url() );
	}

	if ( ! eaccounting()->gateways->is_active( $payment_method ) ) {
		eaccounting_redirect( $invoice->get_url() );
	}

	eaccounting_get_template(
			'content-checkout.php',
			array(
					'invoice'         => $invoice,
					'gateway'         => $payment_method,
					'pay_button_text' => apply_filters( 'eaccounting_pay_button_text', __( 'Pay Now', 'wp-ever-accounting' ) ),
			)
	);
}

/** Login */

if ( ! function_exists( 'eaccounting_login_form' ) ) {

	/**
	 * Output the Login Form.
	 *
	 * @param array $args Arguments.
	 */
	function eaccounting_login_form( $args = array() ) {

		$defaults = array(
				'message'  => '',
				'redirect' => '',
				'hidden'   => false,
		);

		$args = wp_parse_args( $args, $defaults );

		eaccounting_get_template( 'global/form-login.php', $args );
	}
}

