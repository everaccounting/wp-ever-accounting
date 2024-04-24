<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 *
 * @since 1.0.0
 * @package StarterPlugin
 */
class Admin {

	/**
	 * Admin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'buffer_start' ), 1 );
		add_filter( 'admin_body_class', array( $this, 'body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), PHP_INT_MAX );
		add_filter( 'update_footer', array( $this, 'update_footer' ), PHP_INT_MAX );
	}

	/**
	 * Start output buffering.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function buffer_start() {
		ob_start();
	}

	/**
	 * Add body class.
	 *
	 * @param string $classes Body classes.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function body_class( $classes ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$classes .= ' eac-admin';
		}

		return $classes;
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! in_array( $hook, Utilities::get_screen_ids(), true ) ) {
			return;
		}

		// 3rd party scripts.
		EAC()->scripts->enqueue_style( 'bytekit-core' );
		EAC()->scripts->register_style( 'eac-select2', 'css/select-woo.css' );
		EAC()->scripts->register_script( 'eac-select2', 'js/select-woo.js', array( 'jquery' ) );
		EAC()->scripts->enqueue_style( 'eac-select2' );
		EAC()->scripts->enqueue_script( 'eac-select2' );


		// Core scripts.
		EAC()->scripts->register_script( 'eac-core', 'js/eac-core.js', array( 'jquery' )  );
		EAC()->scripts->register_script( 'eac-admin', 'js/eac-admin.js', array( 'jquery', 'eac-select2' ) );
		EAC()->scripts->register_style( 'eac-admin', 'css/eac-admin.css');
		EAC()->scripts->enqueue_script( 'eac-core' );
		EAC()->scripts->enqueue_script( 'eac-admin' );
		EAC()->scripts->enqueue_style( 'eac-admin' );



		EAC()->scripts->add_data( 'eac-core', 'eac_core_js_vars', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'eac_nonce' ),
			'i18n'     => array(
				'confirm_delete' => __( 'Are you sure you want to delete this item?', 'wp-ever-accounting' ),
			),
		) );
	}

	/**
	 * Request review.
	 *
	 * @param string $text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function admin_footer_text( $text ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			$text = sprintf(
			/* translators: %s: Plugin name */
				__( 'Thank you for using %s!', 'wp-ever-accounting' ),
				'<strong>' . esc_html( EAC()->get_name() ) . '</strong>',
			);
			if ( EAC()->review_url ) {
				$text .= sprintf(
				/* translators: %s: Plugin name */
					__( ' Share your appreciation with a five-star review %s.', 'wp-ever-accounting' ),
					'<a href="' . esc_url( EAC()->review_url ) . '" target="_blank">here</a>'
				);
			}
		}

		return $text;
	}

	/**
	 * Update footer.
	 *
	 * @param string $footer_text Footer text.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function update_footer( $footer_text ) {
		if ( in_array( get_current_screen()->id, Utilities::get_screen_ids(), true ) ) {
			/* translators: 1: Plugin version */
			$footer_text = sprintf( esc_html__( 'Version %s', 'wp-ever-accounting' ), EAC()->get_version() );
		}

		return $footer_text;
	}
}
