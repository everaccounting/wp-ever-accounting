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
 * Form field.
 *
 * @param array $args Field arguments.
 *
 * @since 1.1.6
 * @return void|string String when 'return' argument is passed as true.
 */
function eac_input_field( $args ) {
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
				$items       = eac_get_items( array( 'include' => $args['value'] ) );
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

		if ( $args['required'] ) {
			$label .= ' <abbr class="required" title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '">*</abbr>';
		}

		if ( ! empty( $args['tooltip'] ) ) {
			$label .= '<span class="eac-input__tooltip">' . eac_tooltip( $args['tooltip'] ) . '</span>';
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
 * Upload field.
 *
 * @param array $args Field arguments.
 *
 * @since  1.0.0
 * @return void
 */
function eac_upload_field( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'type'          => 'file',
			'name'          => '',
			'id'            => '',
			'class'         => array(),
			'input_class'   => array(),
			'field_class'   => array(),
			'wrapper'       => true,
			'wrapper_class' => array(),
			'label'         => '',
			'label_class'   => array(),
			'desc'          => '',
			'before'        => '',
			'after'         => '',
			'value'         => '',
			'placeholder'   => '',
			'return'        => false,
			'file_type'     => 'image',
			'limit'         => 1,
			'preview_size'  => 'thumbnail',
			'button_text'   => __( 'Upload', 'wp-ever-accounting' ),
		)
	);

	$args['field_class'][] = 'eac-upload-field';
	$args['field_class'][] = 'eac-upload-field--' . $args['file_type'];

	$attrs = array();

}

/**
 * Get svg icon related to the accounting software.
 *
 * @since 1.0.0
 *
 * @param string $icon Icon name.
 * @param string $size Icon size.
 *
 * @return string
 */
function eac_get_svg_icon( $icon, $size = '16' ) {
	$icons = array(
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
