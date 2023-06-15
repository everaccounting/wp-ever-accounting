<?php

defined( 'ABSPATH' ) || exit();

/**
 * Get template part.
 *
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 *
 * @return void
 */
function eac_get_template_part( $slug, $name = null ) {
	$templates = array();
	if ( $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	/**
	 * Filters the  templates for a given $slug and/or $name combination.
	 *
	 * @param string $templates The list of possible template parts.
	 * @param string $slug The slug of the template part.
	 * @param string $name The name of the template part.
	 */
	$templates = apply_filters( 'ever_accounting_get_template_part', $templates, $slug, $name );

	foreach ( $templates as $template ) {
		$located = eac_locate_template( $template );

		if ( ! empty( $located ) ) {
			load_template( $located, false );
			break;
		}
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
function eac_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = ever_accounting()->get_template_path();
	}

	if ( ! $default_path ) {
		$default_path = ever_accounting()->get_template_path();
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			'accounting/' . $template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'ever_accounting_locate_template', $template, $template_name, $template_path );
}


/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array  $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 */
function eac_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	$template = eaccounting_locate_template( $template_name, $template_path, $default_path );

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'ever_accounting_get_template', $template, $template_name, $args, $template_path, $default_path );

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

	do_action( 'ever_accounting_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'ever_accounting_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Like eaccounting_get_template, but returns the HTML instead of outputting.
 *
 * @param string $template_name Template name.
 * @param array  $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @see   eaccounting_get_template
 * @since 1.0.2
 * @return string
 */
function eac_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	eac_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}

/**
 * Output endpoint header.
 *
 * @since 1.1.6
 * @return void
 */
function eac_get_header() {
	eac_get_template_part( 'header' );
}

/**
 * Output endpoint footer.
 *
 * @since 1.1.6
 * @return void
 */
function eac_get_footer() {
	eac_get_template_part( 'footer' );
}

/**
 * Dropdown menu.
 *
 * @param array $items Array of actions.
 * @param array $args Array of arguments.
 *
 * @since 1.1.6
 *
 * @return void
 */
function eac_dropdown_menu( $items, $args = array() ) {
	$defaults = array(
		'button_class'  => 'button-secondary',
		'button_text'   => '',
		'button_icon'   => '',
		'dropdown_icon' => 'dashicons-ellipsis',
		'button_css'    => '',
		'button_id'     => 'eac-dropdown' . wp_rand( 1, 1000 ),
		'button_attrs'  => array(),
	);

	$args                  = wp_parse_args( $args, $defaults );
	$args['button_class'] .= ' eac-dropdown__button';

	if ( ! empty( $args['button_css'] ) ) {
		$args['button_attrs']['style'] .= $args['button_css'];
	}

	$attrs = array();
	foreach ( $args['button_attrs'] as $attr => $value ) {
		$attrs[] = $attr . '="' . esc_attr( $value ) . '"';
	}
	if ( empty( $items ) ) {
		return;
	}
	?>
	<div class="eac-dropdown">
		<button class="<?php echo esc_attr( $args['button_class'] ); ?>" id="<?php echo esc_attr( $args['button_id'] ); ?>" <?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>>
			<?php if ( $args['button_icon'] ) : ?>
				<span class="dashicons <?php echo esc_attr( $args['button_icon'] ); ?>"></span>
			<?php endif; ?>
			<?php if ( $args['button_text'] ) : ?>
				<?php echo esc_html( $args['button_text'] ); ?>
			<?php endif; ?>
			<?php if ( $args['dropdown_icon'] ) : ?>
				<span class="dashicons <?php echo esc_attr( $args['dropdown_icon'] ); ?>"></span>
			<?php endif; ?>
		</button>
		<ul class="eac-dropdown__menu">
			<?php foreach ( $items as $item ) : ?>
				<?php $item_attrs = array(); ?>
				<?php if ( ! empty( $item['attrs'] ) ) : ?>
					<?php foreach ( $item['attrs'] as $attr => $value ) : ?>
						<?php $item_attrs[] = $attr . '="' . esc_attr( $value ) . '"'; ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo wp_kses_post( implode( ' ', $item_attrs ) ); ?>><?php echo esc_html( $item['text'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php
}
