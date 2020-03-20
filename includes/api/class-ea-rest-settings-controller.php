<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Settings_Controller extends EAccounting_REST_Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * @since 1.0.0
	 */

	/**
	 * @since 1.0.0
	 */
	public function register_routes() {
		$get_item_args = array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<section>[\w]+)',
			array(
				'args'   => array(
					'section' => array(
						'description' => __( 'Unique identifier for the object.', 'wp-ever-accounting' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => $get_item_args,
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}


	public function get_settings( $request ) {
		$section = sanitize_text_field( $request['section'] );
		$settings = eaccounting_get_section_settings($section);
		return rest_ensure_response( $settings );
	}


	public function update_settings( $request ) {
		$section = sanitize_text_field( $request['section'] );

	}


	public function get_general_settings( $request ) {
		$settings = array(
			'company_name'       => get_option( 'ea_company_name', sanitize_key( site_url() ) ),
			'company_email'      => get_option( 'ea_company_email', get_option( 'admin_email' ) ),
			'company_tax_number' => get_option( 'company_tax_number', '' ),
			'company_phone'      => get_option( 'ea_company_phone', '' ),
			'company_address'    => get_option( 'ea_company_address', '' ),
			'company_city'       => get_option( 'ea_company_city', '' ),
			'company_state'      => get_option( 'ea_company_state', '' ),
			'company_postcode'   => get_option( 'ea_company_postcode', '' ),
			'company_country'    => get_option( 'ea_company_country', '' ),
			'financial_start'    => get_option( 'ea_financial_start', '01-01' ),
			'date_format'        => get_option( 'ea_date_format', 'F j, Y' ),
		);

		return rest_ensure_response( $settings );
	}

}
