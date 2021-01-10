<?php
/**
 * Get template part.
 *
 * @param mixed $slug Template slug.
 * @param string $name Template name (default: '').
 */
function eaccounting_get_template_part( $slug, $name = null ) {
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
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string
 * @see   eaccounting_get_template
 *
 * @since 1.0.2
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
 */
function eaccounting_get_admin_template( $template_name, $args = array() ) {

	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	$template_name = str_replace( '.php', '', $template_name );
	$file          = apply_filters( 'eaccounting_admin_template', EACCOUNTING_ABSPATH . '/includes/admin/views/' . $template_name . '.php' );
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
 * @param       $template_name
 * @param array $args
 *
 * @return string
 * @since 1.0.0
 *
 */
function eaccounting_get_admin_template_html( $template_name, $args = array() ) {
	ob_start();

	eaccounting_get_admin_template( $template_name, $args );

	return ob_get_clean();
}

<<<<<<< HEAD
function eaccounting_must_head_template() {
	?>
	<div class="eaccounting_header">
		<div class="ea-container">
			<div class="ea-row">
				<div class="ea-col-3">
					<div class="ea-company-logo">
						<img src="<?php echo eaccounting()->settings->get( 'company_logo', eaccounting()->plugin_url( '/assets/images/document-logo.png' ) ); ?>" alt="company-logo" class="company-logo">
					</div>
				</div>
				<div class="ea-col-9">
					<div class="ea-menu">
						<?php //todo menu will come later?>
					</div>
				</div>
			</div>
			<!-- /.row -->

		</div>
		<!-- /.ea-container -->
	</div>
	<?php
}

add_action( 'eaccounting_must_head', 'eaccounting_must_head_template' );

function eaccounting_invoice_top( $invoice ) {
	?>
	<div class="ea-invoice-actions clearfix">
		<div class="ea-invoice-status alignleft">
			<div class="ea-document__status <?php echo sanitize_html_class( $invoice->get_status() ); ?>">
				<span><?php echo esc_html( $invoice->get_status_nicename() ); ?></span>
			</div>
		</div>
		<div class="ea-invoice-buttons alignright">
			<button class="button button-secondary download">
				<span class="dashicons dashicons-printer"></span>
				<?php esc_html_e( 'Download', 'wp-ever-accounting' ); ?>
			</button>
			<?php if ( is_user_logged_in() && current_user_can( 'ea_manage_invoice' ) && $invoice->is_editable() ) : ?>
				<a class="button button-primary edit" href="<?php echo admin_url( 'admin.php?page=ea-sales&tab=invoices&action=edit&invoice_id=3', 'admin' ) ?>">
					<span class="dashicons dashicons-money-alt"></span>
					<?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

add_action( 'eaccounting_invoice_top', 'eaccounting_invoice_top', 10 );

function eaccounting_invoice_content( $invoice ) {
	eaccounting_get_template( 'invoice/invoice.php', array( 'invoice' => $invoice ) );
}

add_action( 'eaccounting_invoice_content', 'eaccounting_invoice_content', 10 );

function eaccounting_invoice_payment( $invoice ) {
	eaccounting_get_template( 'invoice/invoice-payments.php', array( 'invoice' => $invoice ) );
}

add_action( 'eaccounting_invoice_payment', 'eaccounting_invoice_payment', 10 );

function eaccounting_must_footer_template() {
	?>
	<div class="eaccounting_footer">
		<div class="ea-container">
			<p><?php echo __( ' Thanks for buying from ', 'wp-ever-accounting' ) . '<a href="' . get_site_url() . '">' . get_bloginfo( 'name' ) . '</a>'; ?></p>
		</div>
	</div>
	<!-- /.eaccounting_footer -->
	<?php
}

add_action( 'eaccounting_must_footer', 'eaccounting_must_footer_template' );
=======
/**
 * Get base slug.
 *
 * @since 1.1.0
 */
function eaccounting_get_parmalink_base(){
	return apply_filters('eaccounting_parmalink_base', 'eaccounting');
}
>>>>>>> 4be640eb27a07281c162f43bf78ab12df0942184
