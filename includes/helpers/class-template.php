<?php
/**
 * Template Helper class.
 *
 * @since       1.1.4
 * @package     Ever_Accounting
 * @class       Template
 */

namespace Ever_Accounting\Helpers;

use Ever_Accounting\Bill;
use Ever_Accounting\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Template
*/
class Template {

	/**
	 * Get template part.
	 *
	 * @param mixed  $slug Template slug.
	 * @param string $name Template name (default: '').
	 *
	 * @since 1.1.0
	 */
	public static function get_template_part( $slug, $name = null ) {
		if ( $name ) {
			$template = locate_template(
				array(
					"{$slug}-{$name}.php",
					ever_accounting_template_path() . "{$slug}-{$name}.php",
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
					ever_accounting_template_path() . "{$slug}.php",
				)
			);
		}

		// Allow 3rd party plugins to filter template file from their plugin.
		$template = apply_filters( 'ever_accounting_get_template_part', $template, $slug, $name );

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
	 * @since 1.1.0
	 */
	public static function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = ever_accounting_template_path();
		}

		if ( ! $default_path ) {
			$default_path = ever_accounting_plugin_path() . '/templates/';
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
		return apply_filters( 'ever_accounting_locate_template', $template, $template_name, $template_path );
	}

	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args Arguments. (default: array).
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 *
	 * @since 1.1.0
	 */
	public static function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		$template = self::locate_template( $template_name, $template_path, $default_path );

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
	 * Like get_template, but returns the HTML instead of outputting.
	 *
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path Default path. (default: '').
	 * @param string $template_name Template name.
	 * @param array  $args Arguments. (default: array).
	 *
	 * @return string
	 * @see   self::get_template
	 *
	 * @since 1.0.2
	 */
	public static function get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		ob_start();
		self::get_template( $template_name, $args, $template_path, $default_path );

		return ob_get_clean();
	}

	/**
	 * Get admin view.
	 *
	 * @param string $template_name Name of the template.
	 * @param array $args Array of arguments.
	 * @param null $path Path of the template.
	 *
	 * @since 1.0.2
	 */
	public static function get_admin_template( $template_name, $args = array(), $path = null ) {

		if ( $args && is_array( $args ) ) {
			extract( $args );
		}
		$template_name = str_replace( '.php', '', $template_name );
		if ( is_null( $path ) ) {
			$path = dirname( EVER_ACCOUNTING_FILE ) . '/includes/admin/views/';
		}
		$template = apply_filters( 'ever_accounting_admin_template', $template_name );
		$file     = $path . $template . '.php';
		if ( ! file_exists( $file ) ) {
			/* Translators: %s file name */
			ever_accounting_doing_it_wrong( __FUNCTION__, sprintf( __( 'Admin template %s does not exist', 'wp-ever-accounting' ), $file ), null );

			return;
		}
		include $file;
	}

	/**
	 * Render admin template.
	 *
	 * @param string $template_name Template Name.
	 * @param array  $args Arguments.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_admin_template_html( $template_name, $args = array() ) {
		ob_start();

		self::get_admin_template( $template_name, $args );

		return ob_get_clean();
	}

	/**
	 * Get base slug.
	 *
	 * @since 1.1.0
	 */
	public static function get_parmalink_base() {
		return apply_filters( 'ever_accounting_parmalink_base', 'eaccounting' );
	}

	/**
	 * Conditionally render templates.
	 *
	 * @since 1.1.0
	 */
	public static function render_body() {
		$ea_page = get_query_var( 'ea_page' );
		$key     = get_query_var( 'key' );
		switch ( $ea_page ) {
			case 'invoice':
				$id       = get_query_var( 'id' );
				$template = 'single-invoice.php';
				self::get_template(
					$template,
					array(
						'invoice_id' => $id,
						'key'        => $key,
					)
				);
				break;
			case 'bill':
				$id       = get_query_var( 'id' );
				$template = 'single-bill.php';
				self::get_template(
					$template,
					array(
						'bill_id' => $id,
						'key'     => $key,
					)
				);
				break;
			default:
				self::get_template( 'restricted.php' );
				break;
		}
	}

	/**
	 * Load invoice actions in public view
	 *
	 * @param Invoice $invoice Invoice object.
	 *
	 * @since 1.1.0
	*/
	public static function public_invoice_actions( $invoice ) {
		self::get_template( 'invoice-actions.php', array( 'invoice' => $invoice ) );
	}

	/**
	 * Load bill actions in public view
	 *
	 * @param Bill $bill Bill object
	 *
	 * @since 1.1.0
	 */
	public static function public_bill_actions( $bill ) {
		self::get_template( 'bill-actions.php', array( 'bill' => $bill ) );
	}
}
