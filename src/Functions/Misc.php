<?php

defined( 'ABSPATH' ) || exit();

/**
 * Get all countries
 * since 1.0.0
 *
 * @return array
 */
function eac_get_countries() {
	$countries = array(
		'AF' => 'Afghanistan',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AS' => 'American Samoa',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BY' => 'Belarus',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BO' => 'Bolivia',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BV' => 'Bouvet Island',
		'BR' => 'Brazil',
		'IO' => 'British Indian Ocean Territory',
		'BN' => 'Brunei Darussalam',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'CV' => 'Cape Verde',
		'KY' => 'Cayman Islands',
		'CF' => 'Central African Republic',
		'TD' => 'Chad',
		'CL' => 'Chile',
		'CN' => 'China',
		'CX' => 'Christmas Island',
		'CC' => 'Cocos (Keeling) Islands',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'CD' => 'Congo, the Democratic Republic of the',
		'CK' => 'Cook Islands',
		'CR' => 'Costa Rica',
		'CI' => "Cote D'Ivoire",
		'HR' => 'Croatia',
		'CU' => 'Cuba',
		'CY' => 'Cyprus',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'DJ' => 'Djibouti',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'EC' => 'Ecuador',
		'EG' => 'Egypt',
		'SV' => 'El Salvador',
		'GQ' => 'Equatorial Guinea',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FO' => 'Faroe Islands',
		'FJ' => 'Fiji',
		'FI' => 'Finland',
		'FR' => 'France',
		'GF' => 'French Guiana',
		'PF' => 'French Polynesia',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GE' => 'Georgia',
		'DE' => 'Germany',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GL' => 'Greenland',
		'GD' => 'Grenada',
		'GP' => 'Guadeloupe',
		'GU' => 'Guam',
		'GT' => 'Guatemala',
		'GN' => 'Guinea',
		'GW' => 'Guinea-Bissau',
		'GY' => 'Guyana',
		'HT' => 'Haiti',
		'HM' => 'Heard Island and Mcdonald Islands',
		'VA' => 'Holy See (Vatican City State)',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'HU' => 'Hungary',
		'IS' => 'Iceland',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IR' => 'Iran, Islamic Republic of',
		'IQ' => 'Iraq',
		'IE' => 'Ireland',
		'IL' => 'Israel',
		'IT' => 'Italy',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'JO' => 'Jordan',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KI' => 'Kiribati',
		'KP' => "Korea, Democratic People's Republic of",
		'KR' => 'Korea, Republic of',
		'KW' => 'Kuwait',
		'KG' => 'Kyrgyzstan',
		'LA' => "Lao People's Democratic Republic",
		'LV' => 'Latvia',
		'LB' => 'Lebanon',
		'LS' => 'Lesotho',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'MO' => 'Macao',
		'MK' => 'Macedonia, the Former Yugoslav Republic of',
		'MG' => 'Madagascar',
		'MW' => 'Malawi',
		'MY' => 'Malaysia',
		'MV' => 'Maldives',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MH' => 'Marshall Islands',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'YT' => 'Mayotte',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States of',
		'MD' => 'Moldova, Republic of',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'MS' => 'Montserrat',
		'MA' => 'Morocco',
		'MZ' => 'Mozambique',
		'MM' => 'Myanmar',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'NL' => 'Netherlands',
		'AN' => 'Netherlands Antilles',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'NI' => 'Nicaragua',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'MP' => 'Northern Mariana Islands',
		'NO' => 'Norway',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PH' => 'Philippines',
		'PN' => 'Pitcairn',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'PR' => 'Puerto Rico',
		'QA' => 'Qatar',
		'RE' => 'Reunion',
		'RO' => 'Romania',
		'RU' => 'Russian Federation',
		'RW' => 'Rwanda',
		'SH' => 'Saint Helena',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'PM' => 'Saint Pierre and Miquelon',
		'VC' => 'Saint Vincent and the Grenadines',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'ST' => 'Sao Tome and Principe',
		'SA' => 'Saudi Arabia',
		'SN' => 'Senegal',
		'CS' => 'Serbia and Montenegro',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SB' => 'Solomon Islands',
		'SO' => 'Somalia',
		'ZA' => 'South Africa',
		'GS' => 'South Georgia and the South Sandwich Islands',
		'ES' => 'Spain',
		'LK' => 'Sri Lanka',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SZ' => 'Swaziland',
		'SE' => 'Sweden',
		'CH' => 'Switzerland',
		'SY' => 'Syrian Arab Republic',
		'TW' => 'Taiwan, Province of China',
		'TJ' => 'Tajikistan',
		'TZ' => 'Tanzania, United Republic of',
		'TH' => 'Thailand',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'AE' => 'United Arab Emirates',
		'GB' => 'United Kingdom',
		'US' => 'United States',
		'UM' => 'United States Minor Outlying Islands',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'VE' => 'Venezuela',
		'VN' => 'Viet Nam',
		'VG' => 'Virgin Islands, British',
		'VI' => 'Virgin Islands, U.s.',
		'WF' => 'Wallis and Futuna',
		'EH' => 'Western Sahara',
		'YE' => 'Yemen',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe',
	);

	return apply_filters( 'ever_accounting_countries', $countries );
}

/**
 * Get country address format.
 *
 * @param array  $args Arguments.
 * @param string $separator How to separate address lines.
 *
 * @return string
 */
function eac_get_formatted_address( $args = array(), $separator = '<br/>' ) {
	$default_args = array(
		'name'      => '',
		'company'   => '',
		'address_1' => '',
		'city'      => '',
		'state'     => '',
		'postcode'  => '',
		'country'   => '',
	);
	$format       = apply_filters( 'ever_accounting_address_format', "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}" );
	$args         = array_map( 'trim', wp_parse_args( $args, $default_args ) );
	$countries    = eac_get_countries();
	$country      = isset( $countries[ $args['country'] ] ) ? $countries[ $args['country'] ] : $args['country'];
	$replace      = array_map(
		'esc_html',
		array(
			'{name}'      => $args['name'],
			'{company}'   => $args['company'],
			'{address_1}' => $args['address_1'],
			'{address_2}' => $args['address_2'],
			'{city}'      => $args['city'],
			'{state}'     => $args['state'],
			'{postcode}'  => $args['postcode'],
			'{country}'   => $country,
		)
	);

	$formatted_address = str_replace( array_keys( $replace ), $replace, $format );
	// Clean up white space.
	$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
	$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );
	// Break newlines apart and remove empty lines/trim commas and white space.
	$address_lines = array_map( 'trim', array_filter( explode( "\n", $formatted_address ) ) );

	return implode( $separator, $address_lines );
}

/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field( $field ) {
	$default_args = array(
		'type'         => 'text',
		'name'         => '',
		'id'           => '',
		'label'        => '',
		'desc'         => '',
		'tooltip'      => '',
		'placeholder'  => '',
		'required'     => false,
		'readonly'     => false,
		'disabled'     => false,
		'autofocus'    => false,
		'autocomplete' => false,
		'wrapper'      => true,
		'class'        => [],
		'style'        => '',
		'input_class'  => [],
		'input_style'  => '',
		'group_class'  => '',
		'group_style'  => '',
		'options'      => [],
		'attrs'        => [],
		'default'      => '',
		'suffix'       => '',
		'prefix'       => '',
		'return'       => false,
	);

	$field = wp_parse_args( $field, $default_args );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( 'ever_accounting_input_field_args', $field );

	/**
	 * Filter the arguments of a specific form field before it is rendered.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the form field type.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( "ever_accounting_input_field_{$field['type']}_args", $field );

	// Set default name and ID attributes if not provided.
	$field['name'] = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']   = empty( $field['id'] ) ? $field['name'] : $field['id'];

	// Set default value attribute.
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];

	// Custom input attribute handling.
	$attrs          = array();
	$field['attrs'] = array_filter( array_unique( wp_parse_list( $field['attrs'] ) ) );
	foreach ( [ 'readonly', 'disabled', 'required', 'autofocus' ] as $attr_key ) {
		if ( ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$field['attrs']['autocomplete'] = $field['autocomplete'] ? 'on' : 'off';
	}
	if ( ! empty( $field['input_style'] ) ) {
		$field['attrs']['style'] = $field['input_style'];
	}
	foreach ( $field['attrs'] as $attr_key => $attr_value ) {
		$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	// Prepare classes.
	$field['class']       = wp_parse_list( $field['class'] );
	$field['group_class'] = wp_parse_list( $field['group_class'] );
	$field['input_class'] = wp_parse_list( $field['input_class'] );

	switch ( $field['type'] ) {
		case 'select':
		case 'account':
		case 'customer':
		case 'vendor':
		case 'category':
		case 'item':
		case 'invoice':
		case 'payment':
		case 'tax':
		case 'country':
		case 'currency':
			$field['value']       = wp_parse_list( $field['value'] );
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : __( 'Select an option&hellip;', 'wp-ever-accounting' );

			$callback_map = array(
				'account'  => 'eac_get_accounts',
				'customer' => 'eac_get_customers',
				'vendor'   => 'eac_get_vendors',
				'category' => 'eac_get_categories',
				'item'     => 'eac_get_products',
				'invoice'  => 'eac_get_invoices',
				'bill'     => 'eac_get_bills',
				'payment'  => 'eac_get_payments',
				'tax'      => 'eac_get_taxes',
			);

			if ( is_callable( $field['options'] ) ) {
				$field['options'] = call_user_func( $field['options'], $field['value'] );
			} elseif ( array_key_exists( $field['type'], $callback_map ) && is_callable( $callback_map[ $field['type'] ] ) ) {
				$callback               = $callback_map[ $field['type'] ];
				$query_args             = isset( $field['query_args'] ) ? wp_parse_args( $field['query_args'] ) : array();
				$results                = call_user_func( $callback, array_merge( $query_args, array( 'include' => $field['value'] ) ) );
				$field['options']       = wp_list_pluck( $results, 'formatted_name', 'id' );
				$attrs[]                = 'data-query-args="' . esc_attr( wp_json_encode( $query_args ) ) . '"';
				$attrs[]                = 'data-eac-select2="' . esc_attr( $field['type'] ) . '"';
				$field['input_class'][] = 'eac-select-' . esc_attr( $field['type'] );
			} elseif ( 'currency' === $field['type'] ) {
				$results                = eac_get_currencies();
				$field['options']       = wp_list_pluck( $results, 'formatted_name', 'code' );
				$field['input_class'][] = 'eac-select-currency';
				$field['select2']       = true;
			} elseif ( 'country' === $field['type'] ) {
				$field['options']       = eac_get_countries();
				$field['input_class'][] = 'eac-select-country';
				$field['select2']       = true;
			}

			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}
			if ( ! empty( $field['select2'] ) ) {
				$field['input_class'][] = 'eac-select2';
			}

			$options = sprintf(
				'<option value="">%s</option>',
				esc_html( $field['placeholder'] )
			);
			if ( ! empty( $field['placeholder'] ) ) {
				$attrs[] = 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"';
			}
			foreach ( $field['options'] as $value => $label ) {
				$options .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( (string) $value, $field['value'], true ), true, false ),
					esc_html( $label )
				);
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="eac-field__control %s" %s>%s</select>',
				$field['name'],
				$field['id'],
				esc_attr( implode( ' ', $field['input_class'] ) ),
				implode( ' ', $attrs ),
				$options
			);
			break;

		case 'textarea':
			$input = sprintf(
				'<textarea name="%s" id="%s" class="eac-field__control %s" placeholder="%s" %s>%s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['placeholder'] ),
				implode( ' ', $attrs ),
				esc_textarea( $field['value'] )
			);
			break;

		case 'wp_editor':
			$settings = isset( $field['settings'] ) ? $field['settings'] : array();
			ob_start();
			wp_editor(
				$field['value'],
				$field['id'],
				wp_parse_args(
					$settings,
					array(
						'textarea_name'    => $field['name'],
						'textarea_rows'    => 5,
						'teeny'            => true,
						'media_buttons'    => false,
						'quicktags'        => false,
						'drag_drop_upload' => false,
						'editor_class'     => 'eac-field__control eac-field__wysiwyg',
					)
				)
			);
			$input = ob_get_clean();
			break;

		case 'radio':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<fieldset class="eac-field__radios">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="eac-field__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</fieldset>';
			}
			break;

		case 'checkbox':
			$input = sprintf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" class="eac-field__checkbox %3$s" value="1" %4$s %5$s>%6$s</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				checked( $field['value'], 'yes', false ),
				wp_kses_post( implode( ' ', $attrs ) ),
				wp_kses_post( $field['desc'] )
			);

			break;

		case 'checkboxes':
			$input          = '';
			$field['value'] = wp_parse_list( $field['value'] );
			if ( ! empty( $field['options'] ) ) {
				$input = '<fieldset class="eac-field__checkboxes">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $field['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-field__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</fieldset>';
			}

			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__date eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'date_range':
			preg_match( '/(\d{4}-\d{2}-\d{2})-(\d{4}-\d{2}-\d{2})/', $field['value'], $matches );
			$from   = ! empty( $matches[1] ) ? $matches[1] : '';
			$to     = ! empty( $matches[2] ) ? $matches[2] : '';
			$input1 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__range-start eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '_start' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $from ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$input2 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__range-end eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '_end' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $to ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			$input = sprintf(
				'<div class="eac-field__range">%1$s%2$s%3$s</div>',
				$input1,
				'<span class="eac-field__range-sep">-</span>',
				$input2
			);

			break;

		case 'file':
			$allowed_types = ! empty( $field['allowed_types'] ) ? $field['allowed_types'] : 'image';
			$input         = sprintf(
				'<input type="file" name="%1$s" id="%2$s" class="eac-field__file %3$s" value="%4$s" %5$s accept="%6$s">',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_attr( $allowed_types )
			);
			break;

		case 'money':
		case 'price':
			$input = sprintf(
				'<input type="number" name="%1$s" id="%2$s" class="eac-field-money %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-field-hidden %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'text':
		case 'password':
		case 'datetime':
		case 'datetime-local':
		case 'month':
		case 'time':
		case 'week':
		case 'number':
		case 'email':
		case 'url':
		case 'tel':
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-field__input %4$s" value="%5$s" %6$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'callback':
			$input = call_user_func( $field['callback'], $field );
			break;

		default:
			$input = '';
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) && ! empty( $input ) ) {
		if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
			$field['prefix'] = '<span class="eac-field__addon">' . $field['prefix'] . '</span>';
		}

		if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
			$field['suffix'] = '<span class="eac-field__addon">' . $field['suffix'] . '</span>';
		}
		$input = sprintf(
			'<div class="eac-field__group %s" style="%s">%s%s%s</div>',
			esc_attr( implode( ' ', $field['group_class'] ) ),
			esc_attr( $field['group_style'] ),
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( $field['wrapper'] && 'hidden' !== $field['type'] && ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '" class="eac-field__label">' . esc_html( $field['label'] );
			if ( true === $field['required'] ) {
				$label .= '&nbsp;<abbr class="eac-field__required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
			}
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= eac_tooltip( $field['tooltip'] );
			}
			$label .= '</label>';
			$input  = $label . $input;
		}
		if ( ! empty( $field['desc'] ) && ! in_array( $field['type'], array( 'checkbox', 'switch' ), true ) ) {
			$input .= '<p class="eac-field__desc">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="eac-field eac-field--%1$s %2$s" id="eac-field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( implode( ' ', array_unique( array_filter( $field['class'] ) ) ) ),
			esc_attr( $field['id'] ),
			esc_attr( $field['style'] ),
			$input
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( 'ever_accounting_input_field_html', $input, $field );

	/**
	 * Filter the output of the field.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the field type.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( "ever_accounting_input_field_{$field['type']}_html", $input, $field );

	if ( $field['return'] ) {
		return $input;
	} else {
		echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field12347( $field ) {
	$default_args = array(
		'type'         => 'text',
		'name'         => '',
		'id'           => '',
		'label'        => '',
		'desc'         => '',
		'tooltip'      => '',
		'placeholder'  => '',
		'required'     => false,
		'readonly'     => false,
		'disabled'     => false,
		'autofocus'    => false,
		'autocomplete' => false,
		'wrapper'      => true,
		'class'        => [],
		'style'        => '',
		'input_class'  => [],
		'input_style'  => '',
		'options'      => [],
		'attrs'        => [],
		'default'      => '',
		'suffix'       => '',
		'prefix'       => '',
		'return'       => false,
	);

	$field = wp_parse_args( $field, $default_args );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( 'ever_accounting_input_field_args', $field );

	/**
	 * Filter the arguments of a specific form field before it is rendered.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the form field type.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( "ever_accounting_input_field_{$field['type']}_args", $field );

	// Set default name and ID attributes if not provided.
	$field['name'] = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']   = empty( $field['id'] ) ? $field['name'] : $field['id'];

	// Set default value attribute.
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];

	// Custom input attribute handling.
	$attrs          = array();
	$field['attrs'] = array_filter( array_unique( wp_parse_list( $field['attrs'] ) ) );
	foreach ( [ 'readonly', 'disabled', 'required', 'autofocus' ] as $attr_key ) {
		if ( ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$field['attrs']['autocomplete'] = $field['autocomplete'] ? 'on' : 'off';
	}
	if ( ! empty( $field['input_style'] ) ) {
		$field['attrs']['style'] = $field['input_style'];
	}
	foreach ( $field['attrs'] as $attr_key => $attr_value ) {
		$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	// Prepare classes.
	$field['class']       = wp_parse_list( $field['class'] );
	$field['input_class'] = wp_parse_list( $field['input_class'] );

	switch ( $field['type'] ) {
		case 'textarea':
			$input = sprintf(
				'<textarea name="%s" id="%s" class="eac-field__textarea %s" placeholder="%s" %s>%s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['placeholder'] ),
				implode( ' ', $attrs ),
				esc_textarea( $field['value'] )
			);
			break;
		// case 'currency':
		case 'account':
		case 'customer':
		case 'vendor':
		case 'category':
		case 'item':
		case 'invoice':
		case 'bill':
		case 'payment':
		case 'tax':
			$field['value'] = wp_parse_list( $field['value'] );
			$field['value'] = array_map( 'strval', $field['value'] );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}
			if ( 'currency' === $field['type'] ) {
				$currencies       = eac_get_currencies( array( 'code__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $currencies, 'formatted_name', 'code' );
			} elseif ( 'account' === $field['type'] ) {
				$accounts         = eac_get_accounts( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $accounts, 'name', 'id' );
			} elseif ( 'customer' === $field['type'] ) {
				$customers        = eac_get_customers( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $customers, 'formatted_name', 'id' );
			} elseif ( 'vendor' === $field['type'] ) {
				$vendors          = eac_get_vendors( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $vendors, 'formatted_name', 'id' );
			} elseif ( 'category' === $field['type'] ) {
				$subtype          = ! empty( $field['subtype'] ) ? $field['subtype'] : 'expense';
				$categories       = eac_get_categories(
					array(
						'id__in' => $field['value'],
						'type'   => $subtype,
					)
				);
				$field['options'] = wp_list_pluck( $categories, 'formatted_name', 'id' );
				$attrs[]          = 'data-subtype="' . esc_attr( $subtype ) . '"';
			} elseif ( 'item' === $field['type'] ) {
				$items            = eac_get_products( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $items, 'formatted_name', 'id' );
			} elseif ( 'invoice' === $field['type'] ) {
				$invoices         = eac_get_invoices( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $invoices, 'formatted_name', 'id' );
			} elseif ( 'bill' === $field['type'] ) {
				$bills            = eac_get_bills( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $bills, 'formatted_name', 'id' );
			} elseif ( 'payment' === $field['type'] ) {
				$payments         = eac_get_payments( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $payments, 'formatted_name', 'id' );
			} elseif ( 'tax' === $field['type'] ) {
				$taxes            = eac_get_taxes( array( 'include' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $taxes, 'formatted_name', 'id' );
			}
			$attrs[]                = 'data-search-type="' . esc_attr( $field['type'] ) . '"';
			$field['input_class'][] = 'eac-select-' . $field['type'];

			$options = '';
			if ( ! empty( $field['placeholder'] ) ) {
				$attrs[]  = 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"';
				$options .= sprintf(
					'<option value="">%s</option>',
					esc_html( $field['placeholder'] )
				);
			}

			foreach ( $field['options'] as $value => $label ) {
				$options .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( (string) $value, $field['value'], true ), true, false ),
					esc_html( $label )
				);
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="eac-field__select %s" %s>%s</select>',
				$field['name'],
				$field['id'],
				esc_attr( implode( ' ', $field['input_class'] ) ),
				implode( ' ', $attrs ),
				$options
			);
			break;

		case 'country':
			$field['options'] = eac_get_countries();
			// no break.
		case 'currency':
			$options          = eac_get_currencies();
			$field['options'] = wp_list_pluck( $options, 'formatted_name', 'code' );
		case 'select2':
			$field['input_class'][] = 'eac-field-select2';
			// no break.
		case 'select':
			$field['value'] = wp_parse_list( $field['value'] );
			$field['value'] = array_map( 'strval', $field['value'] );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}

			if ( ! empty( $field['placeholder'] ) && 'select' !== $field['type'] ) {
				$attrs[] = 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"';
			}

			$options = '';
			foreach ( $field['options'] as $value => $label ) {
				$options .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( $value, $field['value'], true ), true, false ),
					esc_html( $label )
				);
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="eac-field__select %s" %s>%s</select>',
				$field['name'],
				$field['id'],
				esc_attr( implode( ' ', $field['input_class'] ) ),
				implode( ' ', $attrs ),
				$options
			);
			break;
		case 'radio':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<fieldset>';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="eac-field__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</fieldset>';
			}
			break;

		case 'checkbox':
			$input = sprintf(
				'<label><input type="checkbox" name="%1$s" id="%2$s" class="eac-field__checkbox %3$s" value="1" %4$s %5$s>%6$s</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				checked( $field['value'], 'yes', false ),
				wp_kses_post( implode( ' ', $attrs ) ),
				wp_kses_post( $field['desc'] )
			);

			break;

		case 'checkboxes':
			$input = '';
			if ( ! empty( $args['options'] ) ) {
				$input = '<div class="eac-field__checkboxes">';
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $args['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-field__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $args['name'] ),
						esc_attr( $args['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</div>';
			}

			break;

		case 'switch':
			$input = sprintf(
				'<label class="eac-field__switch">
						<input type="checkbox" name="%1$s" id="%2$s" class="%3$s" value="1" %4$s %5$s>
						<span class="eac-field__switch-slider"></span>
						%6$s
				</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				checked( $field['value'], 'yes', false ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_html( $field['desc'] )
			);
			break;

		case 'radio_group':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<div class="eac-field__radio-group">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label class="button"><input type="radio" name="%1$s" id="%2$s" class="eac-field__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</div>';
			}
			break;
		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__date eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'date_range':
			preg_match( '/(\d{4}-\d{2}-\d{2})-(\d{4}-\d{2}-\d{2})/', $field['value'], $matches );
			$from   = ! empty( $matches[1] ) ? $matches[1] : '';
			$to     = ! empty( $matches[2] ) ? $matches[2] : '';
			$input1 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__date-start eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '_start' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $from ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$input2 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-field__date-end eac-field-date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '_end' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $to ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			$input = sprintf(
				'<div class="eac-field-group">%1$s%2$s%3$s</div>',
				$input1,
				'<span class="eac-field__sep">-</span>',
				$input2
			);

			break;
		case 'file':
			$allowed_types = ! empty( $field['allowed_types'] ) ? $field['allowed_types'] : 'image';
			$input         = sprintf(
				'<input type="file" name="%1$s" id="%2$s" class="eac-field__file %3$s" value="%4$s" %5$s accept="%6$s">',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_attr( $allowed_types )
			);
			break;
		case 'money':
		case 'price':
			$input = sprintf(
				'<input type="number" name="%1$s" id="%2$s" class="eac-field-money %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-field-hidden %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		default:
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-field__input %4$s" value="%5$s" %6$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
		if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
			$field['prefix'] = '<span class="eac-field-addon">' . $field['prefix'] . '</span>';
		}

		if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
			$field['suffix'] = '<span class="eac-field-addon">' . $field['suffix'] . '</span>';
		}
		$input = sprintf(
			'<div class="eac-field-group">%s%s%s</div>',
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( $field['wrapper'] && 'hidden' !== $field['type'] && ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '" class="eac-field__label">' . esc_html( $field['label'] );
			if ( true === $field['required'] ) {
				$label .= '&nbsp;<abbr class="eac-field__required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
			}
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= eac_tooltip( $field['tooltip'] );
			}
			$label .= '</label>';
			$input  = $label . $input;
		}
		if ( ! empty( $field['desc'] ) && ! in_array( $field['type'], array( 'checkbox', 'switch' ) ) ) {
			$input .= '<p class="eac-field__desc">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="eac-field eac-field--%1$s %2$s" id="eac-field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( implode( ' ', array_unique( array_filter( $field['class'] ) ) ) ),
			esc_attr( $field['id'] ),
			esc_attr( $field['style'] ),
			$input
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( 'ever_accounting_input_field_html', $input, $field );

	/**
	 * Filter the output of the field.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the field type.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( "ever_accounting_input_field_{$field['type']}_html", $input, $field );

	if ( $field['return'] ) {
		return $input;
	} else {
		echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Input fields
 *
 * @param array $fields Array of fields.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_fields( $fields ) {
	// Go through every fields and if priority is not set, set it to 10.
	foreach ( $fields as $key => $field ) {
		if ( ! isset( $field['priority'] ) ) {
			$fields[ $key ]['priority'] = 10;
		}
	}

	// Sort fields by priority.
	uasort(
		$fields,
		function ( $a, $b ) {
			if ( $a['priority'] === $b['priority'] ) {
				return 0;
			}

			return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
		}
	);

	// Loop through fields and output them.
	foreach ( $fields as $field ) {
		eac_input_field( $field );
	}
}


/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field123456( $field ) {
	$default_args = array(
		'type'         => 'text',
		'name'         => '',
		'id'           => '',
		'label'        => '',
		'desc'         => '',
		'tooltip'      => '',
		'placeholder'  => '',
		'required'     => false,
		'readonly'     => false,
		'disabled'     => false,
		'autofocus'    => false,
		'autocomplete' => false,
		'wrapper'      => true,
		'class'        => [],
		'style'        => '',
		'input_class'  => [],
		'input_style'  => '',
		'options'      => [],
		'attrs'        => [],
		'default'      => '',
		'return'       => false,
	);

	$field = wp_parse_args( $field, $default_args );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( 'ever_accounting_input_field_args', $field );

	/**
	 * Filter the arguments of a specific form field before it is rendered.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the form field type.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( "ever_accounting_input_field_{$field['type']}_args", $field );

	// Set default name and ID attributes if not provided.
	$field['name'] = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']   = empty( $field['id'] ) ? $field['name'] : $field['id'];

	// Set default value attribute.
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];

	// Custom input attribute handling.
	$attrs          = array();
	$field['attrs'] = array_filter( array_unique( wp_parse_list( $field['attrs'] ) ) );
	foreach ( [ 'readonly', 'disabled', 'required', 'autofocus' ] as $attr_key ) {
		if ( ! empty( $field[ $attr_key ] ) ) {
			$field['attrs'][ $attr_key ] = $attr_key;
		}
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$field['attrs']['autocomplete'] = $field['autocomplete'] ? 'on' : 'off';
	}
	if ( ! empty( $field['input_style'] ) ) {
		$field['attrs']['style'] = $field['input_style'];
	}
	foreach ( $field['attrs'] as $attr_key => $attr_value ) {
		$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	// Prepare classes.
	$field['class']       = wp_parse_list( $field['class'] );
	$field['input_class'] = wp_parse_list( $field['input_class'] );

	switch ( $field['type'] ) {
		case 'textarea':
			$input = sprintf(
				'<textarea name="%s" id="%s" class="%s" placeholder="%s" %s>%s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['placeholder'] ),
				implode( ' ', $attrs ),
				esc_textarea( $field['value'] )
			);
			break;
		case 'country':
			$field['options'] = eac_get_countries();
			// no break.
		case 'currency':
		case 'account':
		case 'customer':
		case 'vendor':
		case 'category':
		case 'item':
		case 'invoice':
		case 'bill':
		case 'payment':
		case 'tax_rate':
			if ( 'currency' === $field['type'] ) {
				$currencies       = eac_get_currencies( array( 'code__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $currencies, 'formatted_name', 'code' );
			} elseif ( 'account' === $field['type'] ) {
				$accounts         = eac_get_accounts( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $accounts, 'name', 'id' );
			} elseif ( 'customer' === $field['type'] ) {
				$customers        = eac_get_customers( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $customers, 'formatted_name', 'id' );
			} elseif ( 'vendor' === $field['type'] ) {
				$vendors          = eac_get_vendors( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $vendors, 'formatted_name', 'id' );
			} elseif ( 'category' === $field['type'] ) {
				$subtype          = ! empty( $field['subtype'] ) ? $field['subtype'] : 'expense';
				$categories       = eac_get_categories(
					array(
						'id__in'  => $field['value'],
						'subtype' => $subtype,
					)
				);
				$field['options'] = wp_list_pluck( $categories, 'formatted_name', 'id' );
			} elseif ( 'item' === $field['type'] ) {
				$items            = eac_get_products( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $items, 'formatted_name', 'id' );
			} elseif ( 'invoice' === $field['type'] ) {
				$invoices         = eac_get_invoices( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $invoices, 'formatted_name', 'id' );
			} elseif ( 'bill' === $field['type'] ) {
				$bills            = eac_get_bills( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $bills, 'formatted_name', 'id' );
			} elseif ( 'payment' === $field['type'] ) {
				$payments         = eac_get_payments( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $payments, 'formatted_name', 'id' );
			} elseif ( 'tax_rate' === $field['type'] ) {
				$tax_rates        = eac_get_tax_rates( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $tax_rates, 'formatted_name', 'id' );
			}
			$attrs[]                = 'data-search-type="' . esc_attr( $field['type'] ) . '"';
			$field['input_class'][] = 'eac-select-' . $field['type'];

			// no break.
		case 'select':
		case 'select2':
			$field['value'] = wp_parse_list( $field['value'] );
			$field['value'] = array_map( 'strval', $field['value'] );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}

			if ( ! empty( $field['placeholder'] ) ) {
				// push placeholder to the top of the options array.
				$field['options'] = array( '' => $field['placeholder'] ) + $field['options'];
			}

			$options = '';
			foreach ( $field['options'] as $value => $label ) {
				$options .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( $value, $field['value'], true ), true, false ),
					esc_html( $label )
				);
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="eac-input__select eac-input__%s %s" %s>%s</select>',
				$field['name'],
				$field['id'],
				esc_attr( $field['type'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				implode( ' ', $attrs ),
				$options
			);
			break;
		case 'radio':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="eac-input__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}
			break;

		case 'checkbox':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<div class="eac-input__checkboxes">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $field['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-input__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</div>';
			}

			break;
		case 'checkboxes':
			$input = '';
			if ( ! empty( $args['options'] ) ) {
				$input = '<div class="eac-input__checkboxes">';
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $args['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-input__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $args['name'] ),
						esc_attr( $args['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</div>';
			}

			break;

		case 'switch':
			$input = sprintf(
				'<label class="eac-input__switch">
						<input type="checkbox" name="%1$s" id="%2$s" class="eac-input__checkbox %3$s" value="1" %4$s %5$s>
						<span class="eac-input__slider"></span>
					</label>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				checked( $field['value'], 'yes', false ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'radio_group':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				$input = '<div class="eac-input__radio-group">';
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label class="button"><input type="radio" name="%1$s" id="%2$s" class="eac-input__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $field['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
				$input .= '</div>';
			}
			break;

		case 'date_range':
			preg_match( '/(\d{4}-\d{2}-\d{2})-(\d{4}-\d{2}-\d{2})/', $field['value'], $matches );
			$from   = ! empty( $matches[1] ) ? $matches[1] : '';
			$to     = ! empty( $matches[2] ) ? $matches[2] : '';
			$input1 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '-from' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $from ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$input2 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '-to' ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $to ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			$input = sprintf(
				'<div class="eac-input__date-range">%1$s%2$s%3$s</div>',
				$input1,
				'<span class="eac-input__date-range-sep">-</span>',
				$input2
			);
			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'money':
		case 'price':
			$input = sprintf(
				'<input type="number" name="%1$s" id="%2$s" class="eac-input__money %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-input__hidden %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'file':
			$allowed_types = ! empty( $field['allowed_types'] ) ? $field['allowed_types'] : 'image';
			$input         = sprintf(
				'<input type="file" name="%1$s" id="%2$s" class="eac-input__file %3$s" value="%4$s" %5$s accept="%6$s">',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_attr( $allowed_types )
			);
			break;
		default:
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-input__text %4$s" value="%5$s" %6$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', $field['input_class'] ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
		if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
			$field['prefix'] = '<span class="eac-input__addon">' . $field['prefix'] . '</span>';
		}

		if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
			$field['suffix'] = '<span class="eac-input__addon">' . $field['suffix'] . '</span>';
		}
		$input = sprintf(
			'<div class="eac-input__group">%s%s%s</div>',
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( $field['wrapper'] && 'hidden' !== $field['type'] && ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '" class="form-field__label">' . esc_html( $field['label'] );
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= eac_tooltip( $field['tooltip'] );
			}
			if ( true === $field['required'] ) {
				$label .= '&nbsp;<abbr class="form-field__required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
			}
			$label .= '</label>';
			$input  = $label . $input;
		}
		if ( ! empty( $field['desc'] ) && 'checkbox' !== $field['type'] ) {
			$input .= '<p class="form-field__help">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="form-field eac-input-field-%1$s %2$s" id="eac-input-field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( implode( ' ', array_unique( array_filter( $field['class'] ) ) ) ),
			esc_attr( $field['id'] ),
			esc_attr( $field['css'] ),
			$input
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( 'ever_accounting_input_field_html', $input, $field );

	/**
	 * Filter the output of the field.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the field type.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( "ever_accounting_input_field_{$field['type']}_html", $input, $field );

	if ( $field['return'] ) {
		return $input;
	} else {
		echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}

/**
 * Form field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field1234( $field ) {
	$default_args = array(
		'type'         => 'text',
		'name'         => '',
		'id'           => '',
		'label'        => '',
		'desc'         => '',
		'tooltip'      => '',
		'placeholder'  => '',
		'required'     => false,
		'readonly'     => false,
		'disabled'     => false,
		'autofocus'    => false,
		'autocomplete' => false,
		'wrapper'      => true,
		'class'        => array(),
		'css'          => '',
		'input_class'  => array(),
		'input_css'    => '',
		'options'      => array(),
		'attrs'        => array(),
		'default'      => '',
		'return'       => false,
	);

	$field = wp_parse_args( $field, $default_args );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( 'ever_accounting_input_field_args', $field );
	/**
	 * Filter the arguments of a specific form field before it is rendered.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the form field type.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$field = apply_filters( "ever_accounting_input_field_{$field['type']}_args", $field );

	// Prepare attributes.
	$field['name']  = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']    = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value'] = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['attrs'] = wp_parse_args( $field['attrs'] );

	// Prepare classes.
	$field['class']       = wp_parse_list( $field['class'] );
	$field['input_class'] = wp_parse_list( $field['input_class'] );

	// Custom input attribute handling.
	$attrs = array();
	if ( ! empty( $field['maxlength'] ) ) {
		$field['attrs']['maxlength'] = absint( $field['maxlength'] );
	}
	if ( ! empty( $field['autocomplete'] ) ) {
		$field['attrs']['autocomplete'] = $field['autocomplete'] ? 'on' : 'off';
	}
	if ( true === $field['readonly'] ) {
		$field['attrs']['readonly'] = 'readonly';
	}
	if ( true === $field['autofocus'] ) {
		$field['attrs']['autofocus'] = 'autofocus';
	}
	if ( ! empty( $field['desc'] ) ) {
		$field['attrs']['aria-describedby'] = $field['id'] . '-description';
	}
	if ( true === $field['required'] ) {
		$field['attrs']['required'] = 'required';
	}
	if ( ! empty( $field['placeholder'] ) ) {
		$field['attrs']['placeholder'] = $field['placeholder'];
	}
	if ( ! empty( $field['input_css'] ) ) {
		$field['attrs']['style'] = $field['input_css'];
	}
	foreach ( $field['attrs'] as $attr_key => $attr_value ) {
		$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
	}

	// if prefix or suffix is set, If it does not contain html tag, wrap it with span.

	switch ( $field['type'] ) {
		case 'textarea':
			$input = sprintf(
				'<textarea name="%s" id="%s" class="eac-input-field__%s %s" placeholder="%s" %s>%s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['type'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $field['placeholder'] ),
				implode( ' ', $attrs ),
				esc_textarea( $field['value'] )
			);
			break;
		case 'select':
		case 'select2':
		case 'country':
		case 'currency':
		case 'account':
		case 'customer':
		case 'vendor':
		case 'category':
		case 'item':
		case 'invoice':
		case 'bill':
		case 'payment':
		case 'tax_rate':
			$field['input_class'][] = 'eac-input-field__select';
			$field['value']         = wp_parse_list( $field['value'] );
			$field['value']         = array_map( 'strval', $field['value'] );
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}
			if ( 'country' === $field['type'] ) {
				$field['options'] = eac_get_countries();
			} elseif ( 'currency' === $field['type'] ) {
				$currencies       = eac_get_currencies( array( 'code__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $currencies, 'formatted_name', 'code' );
			} elseif ( 'account' === $field['type'] ) {
				$accounts         = eac_get_accounts( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $accounts, 'name', 'id' );
			} elseif ( 'customer' === $field['type'] ) {
				$customers        = eac_get_customers( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $customers, 'formatted_name', 'id' );
			} elseif ( 'vendor' === $field['type'] ) {
				$vendors          = eac_get_vendors( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $vendors, 'formatted_name', 'id' );
			} elseif ( 'category' === $field['type'] ) {
				$subtype          = ! empty( $field['subtype'] ) ? $field['subtype'] : 'expense';
				$categories       = eac_get_categories(
					array(
						'id__in'  => $field['value'],
						'subtype' => $subtype,
					)
				);
				$field['options'] = wp_list_pluck( $categories, 'formatted_name', 'id' );
			} elseif ( 'item' === $field['type'] ) {
				$items            = eac_get_products( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $items, 'formatted_name', 'id' );
			} elseif ( 'invoice' === $field['type'] ) {
				$invoices         = eac_get_invoices( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $invoices, 'formatted_name', 'id' );
			} elseif ( 'bill' === $field['type'] ) {
				$bills            = eac_get_bills( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $bills, 'formatted_name', 'id' );
			} elseif ( 'payment' === $field['type'] ) {
				$payments         = eac_get_payments( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $payments, 'formatted_name', 'id' );
			} elseif ( 'tax_rate' === $field['type'] ) {
				$tax_rates        = eac_get_tax_rates( array( 'id__in' => $field['value'] ) );
				$field['options'] = wp_list_pluck( $tax_rates, 'formatted_name', 'id' );
			}

			$options_html = '';
			if ( ! empty( $field['placeholder'] ) ) {
				$options_html .= sprintf(
					'<option value="">%s</option>',
					esc_html( $field['placeholder'] )
				);
			}

			foreach ( $field['options'] as $value => $label ) {
				$options_html .= sprintf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $value ),
					selected( in_array( $value, $field['value'], true ), true, false ),
					esc_html( $label )
				);
			}

			$input = sprintf(
				'<select name="%s" id="%s" class="eac-input-field__%s %s" %s>%s</select>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['type'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				implode( ' ', $attrs ),
				$options_html
			);
			break;

		case 'radio':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="eac-input-field__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'checkbox':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $field['value'], false );
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s" id="%2$s" class="eac-input-field__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'checkboxes':
			$input = '';
			if ( ! empty( $field['options'] ) ) {
				foreach ( $field['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $field['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-input-field__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $field['name'] ),
						esc_attr( $field['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'date_range':
			preg_match( '/(\d{4}-\d{2}-\d{2})-(\d{4}-\d{2}-\d{2})/', $field['value'], $matches );
			$from   = ! empty( $matches[1] ) ? $matches[1] : '';
			$to     = ! empty( $matches[2] ) ? $matches[2] : '';
			$input1 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input-field__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '-from' ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $from ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$input2 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input-field__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] . '-to' ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $to ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			$input = sprintf(
				'<div class="eac-input-field__date-range">%1$s%2$s%3$s</div>',
				$input1,
				'<span class="eac-input-field__date-range-sep">-</span>',
				$input2
			);
			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input-field__date %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'money':
		case 'price':
			$input = sprintf(
				'<input type="number" name="%1$s" id="%2$s" class="eac-input-field__input eac-input-field__%3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-input-field__hidden %3$s" value="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
		default:
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-input-field__input eac-input-field__%1$s %4$s" value="%5$s" %6$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( implode( ' ', array_unique( array_filter( $field['input_class'] ) ) ) ),
				esc_attr( $field['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;
	}

	if ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) {
		if ( ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ) {
			$field['prefix'] = '<span class="eac-input-field__addon">' . $field['prefix'] . '</span>';
		}

		if ( ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ) {
			$field['suffix'] = '<span class="eac-input-field__addon">' . $field['suffix'] . '</span>';
		}
		$input = sprintf(
			'<div class="eac-input-field__group">%s%s%s</div>',
			$field['prefix'],
			$input,
			$field['suffix']
		);
	}

	if ( $field['wrapper'] && 'hidden' !== $field['type'] && ! empty( $input ) ) {
		if ( ! empty( $field['label'] ) ) {
			$label = '<label for="' . esc_attr( $field['id'] ) . '" class="eac-input-field__label">' . esc_html( $field['label'] );
			if ( ! empty( $field['tooltip'] ) ) {
				$label .= ' <abbr class="eac-input-field__tooltip" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
			}
			if ( true === $field['required'] ) {
				$label .= '<span class="eac-input-field__required">*</span>';
			}
			$label .= '</label>';
			$input  = $label . $input;
		}
		if ( ! empty( $field['desc'] ) && 'checkbox' !== $field['type'] ) {
			$input .= '<p class="eac-input-field__desc">' . esc_html( $field['desc'] ) . '</p>';
		}

		$input = sprintf(
			'<div class="eac-input-field eac-input-field-%1$s %2$s" id="eac-input-field-%3$s" style="%4$s">%5$s</div>',
			esc_attr( $field['type'] ),
			esc_attr( implode( ' ', array_unique( array_filter( $field['class'] ) ) ) ),
			esc_attr( $field['id'] ),
			esc_attr( $field['css'] ),
			$input
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( 'ever_accounting_input_field_html', $input, $field );

	/**
	 * Filter the output of the field.
	 *
	 * The dynamic portion of the hook name, `$field['type']`, refers to the field type.
	 *
	 * @param string $output The field HTML.
	 * @param array  $field The field arguments.
	 */
	$input = apply_filters( "ever_accounting_input_field_{$field['type']}_html", $input, $field );

	if ( $field['return'] ) {
		return $input;
	} else {
		echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}

/**
 * Form field.
 *
 * @param array $args Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field123( $args ) {
	$defaults = array(
		'type'          => 'text',
		'name'          => '',
		'id'            => '',
		'label'         => '',
		'desc'          => '',
		'tooltip'       => '',
		'css'           => '',
		'placeholder'   => '',
		'maxlength'     => false,
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
		'autocomplete'  => false,
		'autofocus'     => false,
		'wrapper_class' => array(),
		'label_class'   => array(),
		'field_class'   => array(),
		'input_class'   => array(),
		'options'       => array(),
		'attrs'         => array(),
		'default'       => '',
		'wrapper'       => true,
		'return'        => false,
	);

	$args = wp_parse_args( $args, $defaults );

	/**
	 * Filter the arguments of a form field before it is rendered.
	 *
	 * @param array $args Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$args = apply_filters( 'ever_accounting_input_field_args', $args );
	/**
	 * Filter the arguments of a specific form field before it is rendered.
	 *
	 * The dynamic portion of the hook name, `$args['type']`, refers to the form field type.
	 *
	 * @param array $args Arguments used to render the form field.
	 *
	 * @since 1.1.6
	 */
	$args = apply_filters( "ever_accounting_input_field_args_{$args['type']}", $args );

	// If id is not set use the name.
	if ( empty( $args['name'] ) ) {
		$args['name'] = $args['id'];
	}

	if ( empty( $args['id'] ) ) {
		$args['id'] = $args['name'];
	}

	if ( empty( $args['value'] ) ) {
		$args['value'] = $args['default'];
	}

	if ( is_string( $args['field_class'] ) ) {
		$args['field_class'] = wp_parse_list( $args['field_class'] );
	}

	if ( is_string( $args['wrapper_class'] ) ) {
		$args['wrapper_class'] = wp_parse_list( $args['wrapper_class'] );
	}

	if ( is_string( $args['label_class'] ) ) {
		$args['label_class'] = wp_parse_list( $args['label_class'] );
	}
	if ( is_string( $args['input_class'] ) ) {
		$args['input_class'] = wp_parse_list( $args['input_class'] );
	}

	// Custom attribute handling.
	$attrs         = array();
	$args['attrs'] = array_filter( (array) $args['attrs'], 'is_string', ARRAY_FILTER_USE_KEY );
	if ( ! empty( $args['maxlength'] ) ) {
		$args['attrs']['maxlength'] = absint( $args['maxlength'] );
	}
	if ( ! empty( $args['autocomplete'] ) ) {
		$args['attrs']['autocomplete'] = $args['autocomplete'] ? 'on' : 'off';
	}
	if ( true === $args['readonly'] ) {
		$args['attrs']['readonly'] = 'readonly';
	}
	if ( true === $args['autofocus'] ) {
		$args['attrs']['autofocus'] = 'autofocus';
	}
	if ( ! empty( $args['desc'] ) ) {
		$args['attrs']['aria-describedby'] = $args['id'] . '-description';
	}
	if ( true === $args['required'] ) {
		$args['attrs']['required'] = 'required';
	}
	if ( ! empty( $args['placeholder'] ) ) {
		$args['attrs']['placeholder'] = $args['placeholder'];
	}
	if ( ! empty( $args['css'] ) ) {
		$args['attrs']['style'] = $args['css'];
	}
	if ( ! empty( $args['attrs'] ) ) {
		foreach ( $args['attrs'] as $attr_key => $attr_value ) {
			$attrs[] = esc_attr( $attr_key ) . '="' . esc_attr( $attr_value ) . '"';
		}
	}

	switch ( $args['type'] ) {
		case 'textarea':
			$input = sprintf(
				'<textarea name="%1$s" id="%2$s" class="eac-input__textarea %3$s" placeholder="%4$s" %5$s>%6$s</textarea>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $args['placeholder'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_textarea( $args['value'] )
			);
			break;
		case 'select':
		case 'select2':
		case 'country':
		case 'currency':
		case 'account':
		case 'customer':
		case 'vendor':
		case 'category':
		case 'item':
		case 'invoice':
		case 'bill':
			$args['value'] = wp_parse_list( $args['value'] );
			$args['value'] = array_map( 'strval', $args['value'] );
			if ( ! empty( $args['multiple'] ) ) {
				$args['name'] .= '[]';
				$attrs[]       = 'multiple="multiple"';
			}
			$args['input_class'][] = 'eac-select__' . sanitize_html_class( $args['type'] );
			$data_search           = false;

			if ( 'country' === $args['type'] ) {
				$args['options'] = eac_get_countries();
			} elseif ( 'currency' === $args['type'] ) {
				$data_search = $args['type'];
				$currencies  = eac_get_currencies( array( 'code__in' => $args['value'] ) );
				foreach ( $currencies as $currency ) {
					$args['options'][ $currency->get_code() ] = $currency->get_formatted_name();
				}
			} elseif ( 'account' === $args['type'] ) {
				$data_search = $args['type'];
				$accounts    = eac_get_accounts( array( 'include' => $args['value'] ) );
				foreach ( $accounts as $account ) {
					$args['options'][ $account->get_id() ] = $account->get_formatted_name();
				}
			} elseif ( 'customer' === $args['type'] ) {
				$data_search = $args['type'];
				$customers   = eac_get_customers( array( 'include' => $args['value'] ) );
				foreach ( $customers as $customer ) {
					$args['options'][ $customer->get_id() ] = $customer->get_formatted_name();
				}
			} elseif ( 'vendor' === $args['type'] ) {
				$data_search = $args['type'];
				$vendors     = eac_get_vendors( array( 'include' => $args['value'] ) );
				foreach ( $vendors as $vendor ) {
					$args['options'][ $vendor->get_id() ] = $vendor->get_formatted_name();
				}
			} elseif ( 'category' === $args['type'] ) {
				$data_search = $args['type'];
				$subtype     = ! empty( $args['subtype'] ) ? $args['subtype'] : 'expense';
				$categories  = eac_get_categories(
					array(
						'include' => $args['value'],
						'type'    => $subtype,
					)
				);
				foreach ( $categories as $category ) {
					$args['options'][ $category->get_id() ] = $category->get_formatted_name();
				}
				$attrs[] = 'data-subtype="' . esc_attr( $args['subtype'] ) . '"';
			} elseif ( 'item' === $args['type'] ) {
				$data_search = $args['type'];
				$items       = eac_get_products( array( 'include' => $args['value'] ) );
				foreach ( $items as $item ) {
					$args['options'][ $item->get_id() ] = $item->get_formatted_name();
				}
			} elseif ( 'invoice' === $args['type'] ) {
				$data_search = $args['type'];
				$invoices    = eac_get_invoices( array( 'include' => $args['value'] ) );
				foreach ( $invoices as $invoice ) {
					$args['options'][ $invoice->get_id() ] = $invoice->get_invoice_number();
				}
			} elseif ( 'bill' === $args['type'] ) {
				$data_search = $args['type'];
				$bills       = eac_get_bills( array( 'include' => $args['value'] ) );
				foreach ( $bills as $bill ) {
					$args['options'][ $bill->get_id() ] = $bill->get_bill_number();
				}
			}

			// If we have a data search, we need to add the data-search attribute.
			if ( $data_search ) {
				$attrs[] = 'data-search="' . esc_attr( $data_search ) . '"';
			}

			$options_html = '';
			if ( ! empty( $args['placeholder'] ) ) {
				$options_html .= sprintf(
					'<option value="">%s</option>',
					esc_html( $args['placeholder'] )
				);
			}
			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key    = (string) $option_key;
					$options_html .= sprintf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $option_key ),
						selected( in_array( $option_key, $args['value'], true ), true, false ),
						esc_html( $option_value )
					);
				}
			}

			$input = sprintf(
				'<select name="%1$s" id="%2$s" class="eac-input__select %3$s" %4$s>%5$s</select>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				wp_kses_post( implode( ' ', $attrs ) ),
				$options_html
			);
			if ( ! empty( $args['prefix'] ) ) {
				$input                 = '<span class="eac-input__prefix">' . wp_kses_post( $args['prefix'] ) . '</span>' . $input;
				$args['field_class'][] = 'eac-input--has-prefix';
			}
			if ( ! empty( $args['suffix'] ) ) {
				$input                .= '<span class="eac-input__suffix">' . wp_kses_post( $args['suffix'] ) . '</span>';
				$args['field_class'][] = 'eac-input--has-suffix';
			}

			break;
		case 'radio':
			$input = '';
			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $args['value'], false );
					$input     .= sprintf(
						'<label><input type="radio" name="%1$s" id="%2$s" class="eac-input__radio %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $args['name'] ),
						esc_attr( $args['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $args['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'checkbox':
			$input = '';
			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = checked( $option_key, $args['value'], false );
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s" id="%2$s" class="eac-input__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $args['name'] ),
						esc_attr( $args['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $args['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'checkboxes':
			$input = '';
			if ( ! empty( $args['options'] ) ) {
				foreach ( $args['options'] as $option_key => $option_value ) {
					$option_key = (string) $option_key;
					$checked    = in_array( $option_key, $args['value'], true ) ? 'checked="checked"' : '';
					$input     .= sprintf(
						'<label><input type="checkbox" name="%1$s[]" id="%2$s" class="eac-input__checkbox %3$s" value="%4$s" %5$s %6$s>%7$s</label>',
						esc_attr( $args['name'] ),
						esc_attr( $args['id'] . '-' . $option_key ),
						esc_attr( implode( ' ', $args['input_class'] ) ),
						esc_attr( $option_key ),
						$checked,
						wp_kses_post( implode( ' ', $attrs ) ),
						esc_html( $option_value )
					);
				}
			}

			break;

		case 'date_range':
			preg_match( '/(\d{4}-\d{2}-\d{2})-(\d{4}-\d{2}-\d{2})/', $args['value'], $matches );
			$from   = ! empty( $matches[1] ) ? $matches[1] : '';
			$to     = ! empty( $matches[2] ) ? $matches[2] : '';
			$input1 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] . '-from' ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $from ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$input2 = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] . '-to' ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $to ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			$input = sprintf(
				'<div class="eac-input__date-range">%1$s%2$s%3$s</div>',
				$input1,
				'<span class="eac-input__date-range-separator">-</span>',
				$input2
			);

			break;

		case 'price':
			$input = sprintf(
				'<input type="number" name="%1$s" id="%2$s" class="eac-input__price %3$s" value="%4$s" %5$s>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $args['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			if ( ! empty( $args['prefix'] ) ) {
				$input                 = '<span class="eac-input__prefix">' . wp_kses_post( $args['prefix'] ) . '</span>' . $input;
				$args['field_class'][] = 'eac-input--has-prefix';
			}
			if ( ! empty( $args['suffix'] ) ) {
				$input                .= '<span class="eac-input__suffix">' . wp_kses_post( $args['suffix'] ) . '</span>';
				$args['field_class'][] = 'eac-input--has-suffix';
			}

			break;
		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="eac-input__date %3$s" value="%4$s" %5$s>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $args['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			if ( ! empty( $args['prefix'] ) ) {
				$input                 = '<span class="eac-input__prefix">' . wp_kses_post( $args['prefix'] ) . '</span>' . $input;
				$args['field_class'][] = 'eac-input--has-prefix';
			}
			if ( ! empty( $args['suffix'] ) ) {
				$input                .= '<span class="eac-input__suffix">' . wp_kses_post( $args['suffix'] ) . '</span>';
				$args['field_class'][] = 'eac-input--has-suffix';
			}
			break;
		case 'hidden':
			$input           = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="eac-input__hidden %3$s" value="%4$s" %5$s>',
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $args['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			$args['label']   = '';
			$args['desc']    = '';
			$args['before']  = '';
			$args['after']   = '';
			$args['wrapper'] = false;

			break;
		case 'file':
			$file_type = ! empty( $args['file_type'] ) ? $args['file_type'] : 'image';

		default:
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="eac-input__%1$s %4$s" value="%5$s" %6$s>',
				esc_attr( $args['type'] ),
				esc_attr( $args['name'] ),
				esc_attr( $args['id'] ),
				esc_attr( implode( ' ', $args['input_class'] ) ),
				esc_attr( $args['value'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			if ( ! empty( $args['prefix'] ) ) {
				$input                 = '<span class="eac-input__prefix">' . wp_kses_post( $args['prefix'] ) . '</span>' . $input;
				$args['field_class'][] = 'eac-input--has-prefix';
			}
			if ( ! empty( $args['suffix'] ) ) {
				$input                .= '<span class="eac-input__suffix">' . wp_kses_post( $args['suffix'] ) . '</span>';
				$args['field_class'][] = 'eac-input--has-suffix';
			}

			break;
	}

	$field = sprintf(
		'<div class="eac-input__group eac-input__type-%1$s %2$s">%3$s</div>',
		esc_attr( $args['type'] ),
		esc_attr( implode( ' ', $args['field_class'] ) ),
		$input
	);

	if ( ! empty( $args['label'] ) ) {
		$label = sprintf(
			'<label class="eac-input__label %1$s" for="%2$s">%3$s',
			esc_attr( implode( ' ', $args['label_class'] ) ),
			esc_attr( $args['id'] ),
			esc_html( $args['label'] ),
		);

		if ( ! empty( $args['tooltip'] ) ) {
			$label .= '<span class="eac-input__tooltip">' . eac_tooltip( $args['tooltip'] ) . '</span>';
		}

		if ( $args['required'] ) {
			$label .= ' <abbr class="required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
		}

		$label .= '</label>';

		$field = $label . $field;
	}

	if ( ! empty( $args['before'] ) ) {
		$before = ( is_callable( $args['before'] ) ? call_user_func( $args['before'], $args ) : wp_kses_post( $args['before'] ) );
		$field  = sprintf(
			'<div class="eac-input__before">%1$s</div> %2$s',
			$before,
			$field
		);
	}

	if ( ! empty( $args['desc'] ) && 'checkbox' !== $args['type'] ) {
		$field .= sprintf(
			'<p class="eac-input__description description"> %1$s</p>',
			esc_html( $args['desc'] )
		);
	}

	if ( ! empty( $args['after'] ) ) {
		$after  = ( is_callable( $args['after'] ) ? call_user_func( $args['after'], $args ) : wp_kses_post( $args['after'] ) );
		$field .= sprintf(
			'<div class="eac-input__after">%1$s</div>',
			$after
		);
	}

	if ( ! empty( $args['wrapper'] ) ) {
		$field = sprintf(
			'<div class="eac-input-wrapper %1$s">%2$s</div>',
			esc_attr( implode( ' ', $args['wrapper_class'] ) ),
			$field
		);
	}

	/**
	 * Filter the output of the field.
	 *
	 * @param string $output The field HTML.
	 * @param array  $args The field arguments.
	 */
	$html = apply_filters( 'ever_accounting_input_field_html', $field, $args );

	/**
	 * Filter the output of the field.
	 *
	 * The dynamic portion of the hook name, `$args['type']`, refers to the field type.
	 *
	 * @param string $output The field HTML.
	 * @param array  $args The field arguments.
	 */
	$html = apply_filters( "ever_accounting_input_field_{$args['type']}_html", $html, $args );

	if ( $args['return'] ) {
		return $html;
	} else {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Get svg icon related to the accounting software.
 *
 * @param string $icon Icon name.
 * @param string $size Icon size.
 *
 * @since 1.0.0
 *
 * @return string
 */
function eac_get_svg_icon( $icon, $size = '24' ) {
	$icons = array(
		'logo'          => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M 18 1.609375 C 14.292969 -0.539062 9.707031 -0.539062 6 1.609375 C 2.292969 3.757812 0 7.714844 0 12 C 0 16.285156 2.292969 20.242188 6 22.390625 C 9.707031 24.539062 14.292969 24.539062 18 22.390625 C 21.707031 20.242188 24 16.285156 24 12 C 24 7.714844 21.707031 3.757812 18 1.609375 Z M 18.371094 13.390625 L 17.496094 18.070312 C 17.339844 18.898438 16.621094 19.488281 15.78125 19.488281 L 14.664062 19.488281 L 15.167969 16.894531 C 13.738281 18.347656 11.039062 19.691406 8.964844 19.691406 C 7.65625 19.691406 6.574219 19.222656 5.722656 18.300781 C 4.871094 17.375 4.441406 16.199219 4.441406 14.785156 C 4.441406 12.898438 5.125 11.230469 6.480469 9.78125 L 6.625 9.636719 C 7.980469 8.257812 9.996094 7.273438 11.964844 7.667969 C 13.65625 8.003906 14.914062 9.457031 15.3125 11.089844 L 15.371094 11.292969 C 15.503906 11.84375 15.203125 12.324219 14.652344 12.46875 L 8.484375 14.039062 L 8.484375 13.164062 L 13.90625 11.304688 C 13.824219 11.136719 13.726562 10.96875 13.609375 10.800781 C 13.019531 9.984375 12.226562 9.574219 11.242188 9.574219 C 10.019531 9.574219 8.941406 10.078125 8.039062 11.074219 C 7.714844 11.4375 7.453125 11.808594 7.246094 12.203125 C 7.007812 12.660156 6.851562 13.140625 6.757812 13.644531 C 6.707031 13.945312 6.671875 14.257812 6.671875 14.578125 C 6.671875 15.492188 6.960938 16.246094 7.523438 16.835938 C 8.101562 17.425781 8.832031 17.6875 9.71875 17.710938 C 10.765625 17.746094 12.238281 17.328125 13.296875 16.777344 C 13.894531 16.464844 14.605469 16.5 15.15625 16.882812 L 15.167969 16.894531 C 15.179688 16.824219 15.191406 16.753906 15.214844 16.679688 C 15.503906 15.191406 15.792969 13.714844 16.078125 12.226562 C 16.378906 10.65625 16.65625 9.109375 15.816406 7.65625 C 15.144531 6.46875 13.859375 5.855469 12.527344 5.773438 C 11.460938 5.699219 10.367188 5.929688 9.382812 6.335938 C 9.300781 6.371094 8.460938 6.757812 8.460938 6.769531 L 7.609375 5.257812 C 7.570312 5.171875 9.144531 4.5 9.289062 4.453125 C 9.898438 4.222656 10.523438 4.042969 11.160156 3.9375 C 12.421875 3.707031 13.738281 3.730469 14.976562 4.09375 C 16.621094 4.585938 18.011719 5.84375 18.515625 7.5 C 19.105469 9.382812 18.636719 11.878906 18.371094 13.390625 Z M 18.371094 13.390625" fill="currentColor"/></svg>',
		'calendar'      => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M19.5 7.5H4.5V19.0005C4.5 19.2764 4.72363 19.5 4.9995 19.5H19.0005C19.2764 19.5 19.5 19.2764 19.5 19.0005V7.5ZM3 7.5V4.9995V4.995C3 3.89319 3.89319 3 4.995 3H4.9995H19.0005H19.005C20.1068 3 21 3.89319 21 4.995V4.9995V7.5V19.0005C21 20.1048 20.1048 21 19.0005 21H4.9995C3.89521 21 3 20.1048 3 19.0005V7.5ZM7.5 10.5H9V12H7.5V10.5ZM9 15H7.5V16.5H9V15ZM11.25 10.5H12.75V12H11.25V10.5ZM12.75 15H11.25V16.5H12.75V15ZM15 10.5H16.5V12H15V10.5ZM16.5 15H15V16.5H16.5V15Z" fill="currentColor"/></svg>',
		'category'      => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.1979 8.25L11.2098 6.27363C11.1259 6.10593 10.9545 6 10.767 6H4.995C4.72162 6 4.5 6.22162 4.5 6.495V17.505C4.5 17.7784 4.72162 18 4.995 18H19.0005C19.2764 18 19.5 17.7764 19.5 17.5005V8.7495C19.5 8.47363 19.2764 8.25 19.0005 8.25H12.1979ZM13.125 6.75H19.0005C20.1048 6.75 21 7.64521 21 8.7495V17.5005C21 18.6048 20.1048 19.5 19.0005 19.5H4.995C3.89319 19.5 3 18.6068 3 17.505V6.495C3 5.39319 3.89319 4.5 4.995 4.5H10.767C11.5227 4.5 12.2135 4.92693 12.5514 5.60281L13.125 6.75Z" fill="currentColor"/></svg>',
		'check'         => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.6103 6.43587L10.6406 17.5937L5.51985 13.326L6.48017 12.1737L10.3594 15.4067L17.3897 5.56403L18.6103 6.43587Z" fill="currentColor"/></svg>',
		'chevron_left'  => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.9697 6.96973L7.93933 12.0001L12.9697 17.0304L14.0303 15.9697L10.0606 12.0001L14.0303 8.03039L12.9697 6.96973Z" fill="currentColor"/></svg>',
		'chevron_right' => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.0303 17.0303L16.0607 11.9999L11.0303 6.96961L9.96968 8.03027L13.9394 11.9999L9.96968 15.9696L11.0303 17.0303Z" fill="currentColor"/></svg>',
	);

	$icons = apply_filters( 'ever_accounting_svg_icons', $icons );
	if ( array_key_exists( $icon, $icons ) ) {
		$repl = sprintf( '<svg class="svg-icon" width="%d" height="%d" aria-hidden="true" role="img" focusable="false" ', $size, $size );
		$svg  = preg_replace( '/^<svg /', $repl, trim( $icons[ $icon ] ) ); // Add extra attributes to SVG code.
		$svg  = preg_replace( "/([\n\t]+)/", ' ', $svg ); // Remove newlines & tabs.
		$svg  = preg_replace( '/>\s*</', '><', $svg ); // Remove white space between SVG tags.

		return $svg;
	}

	return null;
}

/**
 * Convert plaintext phone number to clickable phone number.
 *
 * Remove formatting and allow "+".
 * Example and specs: https://developer.mozilla.org/en/docs/Web/HTML/Element/a#Creating_a_phone_link
 *
 * @since 1.1.6
 *
 * @param string $phone Content to convert phone number.
 * @return string Content with converted phone number.
 */
function eac_make_phone_clickable( $phone ) {
	$number = trim( preg_replace( '/[^\d|\+]/', '', $phone ) );

	return $number ? '<a href="tel:' . esc_attr( $number ) . '">' . esc_html( $phone ) . '</a>' : '';
}

/**
 * Generate unique hash.
 *
 * @since 1.1.6
 * @return string
 */
function eac_generate_hash() {
	return md5( uniqid( rand(), true ) );
}
