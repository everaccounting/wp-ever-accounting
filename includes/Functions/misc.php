<?php

defined( 'ABSPATH' ) || exit();

/**
 * Form input field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_form_field( $field ) {
	$defaults = array(
		'type'          => 'text',
		'name'          => '',
		'id'            => '',
		'placeholder'   => '',
		'required'      => false,
		'readonly'      => false,
		'disabled'      => false,
		'autofocus'     => false,
		'class'         => '',
		'style'         => '',
		'options'       => array(),
		'default'       => '',
		'label'         => '',
		'suffix'        => '',
		'prefix'        => '',
		'wrapper_class' => '',
		'wrapper_style' => '',
	);

	$field = wp_parse_args( $field, $defaults );

	/**
	 * Filter the arguments of a form group before it is rendered.
	 *
	 * @param array $field Arguments used to render the form field.
	 *
	 * @since 1.2.0
	 */
	$field = apply_filters( 'eac_form_field_args', $field );

	// Set default name and ID attributes if not provided.
	$field['name']          = empty( $field['name'] ) ? $field['id'] : $field['name'];
	$field['id']            = empty( $field['id'] ) ? $field['name'] : $field['id'];
	$field['value']         = empty( $field['value'] ) ? $field['default'] : $field['value'];
	$field['class']         = array_filter( array_unique( wp_parse_list( $field['class'] ) ) );
	$field['class']         = array_map( 'sanitize_html_class', $field['class'] );
	$field['class']         = implode( ' ', $field['class'] );
	$field['wrapper_class'] = array_filter( array_unique( wp_parse_list( $field['wrapper_class'] ) ) );
	$field['wrapper_class'] = array_map( 'sanitize_html_class', $field['wrapper_class'] );
	$field['wrapper_class'] = implode( ' ', $field['wrapper_class'] );

	// Prepare attributes.
	// Anything that starts with "attr-" will be added to the attributes.
	$attrs = array();
	foreach ( $field as $k => $v ) {
		if ( empty( $k ) || empty( $v ) ) {
			continue;
		}

		if ( is_array( $v ) || is_object( $v ) ) {
			$v = wp_json_encode( $v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP );
		}

		if ( strpos( $k, 'attr-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( str_replace( 'attr-', '', $k ) ), esc_attr( $v ) );
		} elseif ( strpos( $k, 'data-' ) === 0 ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $v ) );
		} elseif ( in_array( $k, array( 'maxlength', 'pattern', 'readonly', 'disabled', 'required', 'autofocus' ), true ) ) {
			$attrs[] = sprintf( '%s="%s"', esc_attr( $k ), esc_attr( $k ) );
		}
	}

	$input = '';
	switch ( $field['type'] ) {
		case 'text':
		case 'email':
		case 'number':
		case 'password':
		case 'url':
			$input = sprintf(
				'<input type="%1$s" name="%2$s" id="%3$s" class="%4$s" value="%5$s" placeholder="%6$s" style="%7$s" %8$s>',
				esc_attr( $field['type'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);
			break;

		case 'select':
			$field['options']     = is_array( $field['options'] ) ? $field['options'] : array();
			$field['value']       = ! empty( $field['value'] ) ? wp_parse_list( $field['value'] ) : array();
			$field['value']       = array_map( 'strval', $field['value'] );
			$field['placeholder'] = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
			if ( ! empty( $field['multiple'] ) ) {
				$field['name'] .= '[]';
				$attrs[]        = 'multiple="multiple"';
			}

			// It may send an option_key and option_value to use in the options.
			// if its an object, it will use the object properties.
			if ( ! empty( $field['option_value'] ) && ! empty( $field['option_label'] ) ) {
				// verify options is an array otherwise we will make it an array.
				if ( ! is_array( $field['options'] ) ) {
					$field['options'] = array();
				}
				$field['options'] = array_filter( $field['options'] );
				$field['options'] = wp_list_pluck( $field['options'], $field['option_label'], $field['option_value'] );
			}

			$input = sprintf(
				'<select name="%1$s" id="%2$s" class="%3$s" style="%4$s" %5$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			if ( ! empty( $field['placeholder'] ) ) {
				$input .= sprintf(
					'<option value="">%s</option>',
					esc_html( $field['placeholder'] )
				);
			}

			foreach ( $field['options'] as $key => $value ) {
				$input .= sprintf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $key ),
					selected( in_array( (string) $key, $field['value'], true ), true, false ),
					esc_html( $value )
				);
			}

			$input .= '</select>';
			break;

		case 'date':
			$input = sprintf(
				'<input type="text" name="%1$s" id="%2$s" class="%3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			break;

		case 'hidden':
			$input = sprintf(
				'<input type="hidden" name="%1$s" id="%2$s" class="%3$s" value="%4$s" placeholder="%5$s" style="%6$s" %7$s>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['value'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) )
			);

			break;

		case 'textarea':
			$rows  = ! empty( $field['rows'] ) ? absint( $field['rows'] ) : 4;
			$cols  = ! empty( $field['cols'] ) ? absint( $field['cols'] ) : 50;
			$input = sprintf(
				'<textarea name="%1$s" id="%2$s" class="%3$s" placeholder="%4$s" rows="%5$s" cols="%6$s" style="%7$s" %8$s>%9$s</textarea>',
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				esc_attr( $field['class'] ),
				esc_attr( $field['placeholder'] ),
				esc_attr( $rows ),
				esc_attr( $cols ),
				esc_attr( $field['style'] ),
				wp_kses_post( implode( ' ', $attrs ) ),
				esc_textarea( $field['value'] )
			);

			break;

		case 'wp_editor':
			$settings = isset( $field['settings'] ) ? $field['settings'] : array();
			$settings = wp_parse_args(
				$settings,
				array(
					'textarea_name' => $field['name'],
					'textarea_rows' => 10,
				)
			);
			ob_start();
			wp_editor(
				$field['value'],
				$field['id'],
				$settings,
			);
			$input = ob_get_clean();
			break;

		case 'file':
			$field = wp_parse_args(
				$field,
				array(
					'button_class' => '',
					'button_label' => __( 'Choose File', 'wp-ever-accounting' ),
					'mime_types'   => '',
				)
			);
			$file  = array(
				'icon'     => '',
				'title'    => '',
				'url'      => '',
				'filename' => '',
				'filesize' => '',
			);
			if ( ! empty( $field['value'] ) ) {
				// get attachment.

				$post = get_post( $field['value'] );
				if ( $post && 'attachment' === $post->post_type ) {
					$meta            = wp_get_attachment_metadata( $post->ID );
					$attached_file   = get_attached_file( $post->ID );
					$field['class'] .= ' has--file';
					// update.
					$file['icon']     = wp_mime_type_icon( $post->ID );
					$file['title']    = get_the_title( $post->ID );
					$file['url']      = wp_get_attachment_url( $post->ID );
					$file['filename'] = wp_basename( $attached_file );
				}
			}

			$input = sprintf(
				'<div class="eac-file-uploader %1$s" data-mime-types="%2$s" data-file_id="%3$d">
					<input type="hidden" name="%4$s" id="%5$s" value="%6$d" class="eac-file-uploader__input">
					<div class="eac-file-uploader__preview">
						<div class="eac-file-uploader__icon">
							<img src="%7$s" alt="%8$s">
							<a href="#" class="eac-file-uploader__remove"><span class="dashicons dashicons-trash"></span></a>
						</div>
						<div class="eac-file-uploader__details">
							<a href="%9$s" target="_blank" rel="noopener noreferrer" class="eac-file-uploader__filename">%10$s</a>
						</div>
					</div>
					<button type="button" class="eac-file-uploader__button %11$s">%12$s</button>
				</div>',
				esc_attr( $field['class'] ),
				esc_attr( $field['mime_types'] ),
				absint( $field['value'] ),
				esc_attr( $field['name'] ),
				esc_attr( $field['id'] ),
				absint( $field['value'] ),
				esc_url( $file['icon'] ),
				esc_attr( $file['title'] ),
				esc_url( $file['url'] ),
				esc_html( $file['filename'] ),
				esc_attr( $field['button_class'] ),
				esc_html( $field['button_label'] ),
			);

			break;

	}

	// Add prefix and suffix.
	if ( ! empty( $input ) && ( ! empty( $field['prefix'] ) || ! empty( $field['suffix'] ) ) && in_array( $field['type'], array( 'text', 'email', 'number', 'password', 'url', 'select', 'date' ), true ) ) {
		$prefix = ! empty( $field['prefix'] ) && ! preg_match( '/<[^>]+>/', $field['prefix'] ) ? '<span class="eac-form-field__addon">' . $field['prefix'] . '</span>' : $field['prefix'];
		$suffix = ! empty( $field['suffix'] ) && ! preg_match( '/<[^>]+>/', $field['suffix'] ) ? '<span class="eac-form-field__addon">' . $field['suffix'] . '</span>' : $field['suffix'];

		$input = sprintf(
			'<div class="eac-input-group">%1$s%2$s%3$s</div>',
			wp_kses_post( $prefix ),
			$input,
			wp_kses_post( $suffix )
		);
	}

	if ( ! empty( $input ) && ! empty( $field['label'] ) ) {
		$required = true === $field['required'] ? '&nbsp;<abbr title="' . esc_attr__( 'required', 'wp-ever-accounting' ) . '"></abbr>' : '';
		$tooltip  = ! empty( $field['tooltip'] ) ? '&nbsp;' . eac_tooltip( $field['tooltip'] ) : '';
		$input    = sprintf(
			'<label for="%1$s">%2$s%3$s%4$s</label>%5$s',
			esc_attr( $field['id'] ),
			esc_html( $field['label'] ),
			$required,
			$tooltip,
			$input
		);
	}

	if ( ! empty( $input ) && ! empty( $field['desc'] ) ) {
		$input .= sprintf( '<p class="description">%s</p>', wp_kses_post( $field['desc'] ) );
	}

	if ( ! empty( $input ) ) {
		printf(
			'<div class="eac-form-field eac-form-field-%1$s %2$s" style="%3$s">%4$s</div>',
			esc_attr( $field['name'] ),
			esc_attr( $field['wrapper_class'] ),
			esc_attr( $field['wrapper_style'] ),
			$input // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the above code.
		);
	}
}

/**
 * File upload field.
 *
 * @param array $field Field arguments.
 *
 * @since 1.2.0
 * @return void
 */
function eac_file_uploader( $field ) {
	$field = wp_parse_args(
		$field,
		array(
			'value'        => '',
			'name'         => 'attachment_id',
			'button_label' => __( 'Upload attachment', 'wp-ever-accounting' ),
			'mime_types'   => '',
			'icon_url'     => '',
			'title'        => '',
			'src'          => '',
			'url'          => '',
			'filezise'     => '',
			'readonly'     => false,
		)
	);
	$post  = get_post( $field['value'] );
	if ( $post && 'attachment' === $post->post_type ) {
		$meta              = wp_get_attachment_metadata( $post->ID );
		$field['title']    = get_the_title( $post->ID );
		$field['url']      = wp_get_attachment_url( $post->ID );
		$field['filezise'] = size_format( $meta['filesize'] );

		// if the file is an image set the icon to the thumbnail otherwise use the mime type icon.
		if ( wp_attachment_is_image( $post->ID ) ) {
			$field['icon_url'] = wp_get_attachment_image_url( $post->ID, 'thumbnail' );
		} else {
			$field['icon_url'] = wp_mime_type_icon( $post->ID );
		}
	}
	$has_file_class = ! empty( $field['value'] ) ? ' has--file' : '';

	// if readonly and has no value, then return.
	if ( $field['readonly'] && empty( $field['value'] ) ) {
		echo esc_html__( 'No file uploaded', 'wp-ever-accounting' );
		return;
	}

	?>
	<div class="eac-file-upload <?php echo esc_attr( $has_file_class ); ?>">
		<div class="eac-file-upload__dropzone">
			<button class="eac-file-upload__button" type="button"><?php echo esc_html( $field['button_label'] ); ?></button>
			<input class="eac-file-upload__value" type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $field['value'] ); ?>">
		</div>
		<div class="eac-file-upload__preview">
			<div class="eac-file-upload__icon">
				<img src="<?php echo esc_url( $field['icon_url'] ); ?>" alt="<?php echo esc_attr( $field['title'] ); ?>">
			</div>
			<div class="eac-file-upload__info">
				<div class="eac-file-upload__name">
					<a target="_blank" href="<?php echo esc_url( $field['url'] ); ?>"><?php echo esc_html( $field['title'] ); ?></a>
				</div>
				<div class="eac-file-upload__size"><?php echo esc_html( $field['filezise'] ); ?></div>
			</div>
			<?php if ( $field['readonly'] ) : ?>
				<div class="eac-file-upload__action">
					<a href="<?php echo esc_url( $field['url'] ); ?>" class="eac-file-upload__download" download><span class="dashicons dashicons-download"></span></a>
				</div>
			<?php else : ?>
			<div class="eac-file-upload__action">
				<a href="#" class="eac-file-upload__remove"><span class="dashicons dashicons-trash"></span></a>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

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

	return apply_filters( 'eac_countries', $countries );
}
