<?php

class Plugin_Settings {
	public static function get_settings() {
		$settings = array(
			'general'  => array(
				'name'   => __( 'General', 'wp-ever-accounting' ),
				'fields' => array(
					array(
						'name'    => __( 'Company Name', 'wp-ever-accounting' ),
						'id'      => 'company_name',
						'type'    => 'text',
						'desc'    => __( 'The name of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Address', 'wp-ever-accounting' ),
						'id'      => 'company_address',
						'type'    => 'textarea',
						'desc'    => __( 'The address of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Phone', 'wp-ever-accounting' ),
						'id'      => 'company_phone',
						'type'    => 'text',
						'desc'    => __( 'The phone number of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Email', 'wp-ever-accounting' ),
						'id'      => 'company_email',
						'type'    => 'text',
						'desc'    => __( 'The email address of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Website', 'wp-ever-accounting' ),
						'id'      => 'company_website',
						'type'    => 'text',
						'desc'    => __( 'The website of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company VAT Number', 'wp-ever-accounting' ),
						'id'      => 'company_vat_number',
						'type'    => 'text',
						'desc'    => __( 'The VAT number of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Registration Number', 'wp-ever-accounting' ),
						'id'      => 'company_registration_number',
						'type'    => 'text',
						'desc'    => __( 'The registration number of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Logo', 'wp-ever-accounting' ),
						'id'      => 'company_logo',
						'type'    => 'file',
						'desc'    => __( 'The logo of your company.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Logo URL', 'wp-ever-accounting' ),
						'id'      => 'company_logo_url',
						'type'    => 'text',
						'desc'    => __( 'The URL of your company logo.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Company Logo Width', 'wp-ever-accounting' ),
						'id'      => 'company_logo_width',
						'type'    => 'text',
						'desc'    => __( 'The width of your company logo.', 'wp-ever-accounting' ),
						'default' => '',
					),
				),
			),
			'advanced' => array(
				'name'   => __( 'Advanced', 'wp-ever-accounting' ),
				'fields' => array(
					array(
						'name'    => __( 'Enable PDF Invoices', 'wp-ever-accounting' ),
						'id'      => 'enable_pdf_invoices',
						'type'    => 'checkbox',
						'desc'    => __( 'Enable PDF Invoices.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Enable PDF Estimates', 'wp-ever-accounting' ),
						'id'      => 'enable_pdf_estimates',
						'type'    => 'checkbox',
						'desc'    => __( 'Enable PDF Estimates.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name'    => __( 'Enable PDF Payments', 'wp-ever-accounting' ),
						'id'      => 'enable_pdf_payments',
						'type'    => 'checkbox',
						'desc'    => __( 'Enable PDF Payments.', 'wp-ever-accounting' ),
						'default' => '',
					),
					array(
						'name' => __( 'Enable PDF Credit Notes', 'wp-ever-accounting' ),
						'id'   => 'enable_pdf_credit_notes',
						'type' => 'checkbox',
						'desc' => __( 'Enable PDF Credit Notes.', 'wp-ever-accounting' ),
					)
				),
			),
			'html'     => array()
		);

		return apply_filters( 'ever_accounting_settings', $settings );
	}

	public static function output() {
		$settings = self::get_settings();
		foreach ( $settings as $key => $setting ) {
			if ( empty( $setting['id'] ) ) {
				unset( $settings[ $key ] );
				continue;
			}
			$tab_id   = $setting['id'];
			$tab      = apply_filters( 'ever_accounting_settings_' . $tab_id, wp_parse_args( $setting, array(
				'id'       => '',
				'title'    => '',
				'sections' => array(),
				'fields'   => array(),
			) ) );
			$sections = apply_filters( 'ever_accounting_settings_' . $tab['id'] . '_sections', $tab['sections'] );
			if ( empty( $sections ) ) {
				$sections = array(
					'id'    => $tab_id,
					'title' => $tab['title'],
				);
			}


//			if( empty($setting['id'])  || empty($setting['fields']) )


//			$default_section = array(
//				'id'    => $setting['id'],
//				'title' => $setting['title'],
//			);
//			$sections = !empty($setting['sections'])? $setting['sections']:
//			if ( empty( $setting['sections'] ) ) {
//				$setting[ $key ]['sections'] = array(
//					'id'    => $setting['id'],
//					'title' => $setting['title'],
//				);
//			}
//
//
//			$tabs[ $setting->get_id() ] = $setting;
		}


//		$default_tab     = current( $settings )['id'];
//		$current_tab     = isset( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $default_tab;
//		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );
//		$settings_tab    = $settings[ $current_tab ];

	}
}
