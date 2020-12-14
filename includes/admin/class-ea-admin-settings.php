<?php
/**
 * Admin Settings.
 *
 * @since       1.0.2
 * @subpackage  Admin
 * @package     EverAccounting
 */

namespace EverAccounting\Admin;

/**
 * Class Settings
 *
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class Settings {
	/**
	 * Stores all settings.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Settings constructor.
	 *
	 */
	public function __construct() {
		$this->settings = (array) get_option( 'eaccounting_settings', array() );

		// Set up.
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'eaccounting_settings_emails', array( $this, 'register_email_settings' ) );
		add_filter( 'eaccounting_settings_sanitize_text', 'sanitize_text_field' );
		add_filter( 'eaccounting_settings_sanitize_url', 'wp_http_validate_url' );
		add_filter( 'eaccounting_settings_sanitize_checkbox', 'eaccounting_bool_to_string' );
		add_filter( 'eaccounting_settings_sanitize_number', 'absint' );
		add_filter( 'eaccounting_settings_sanitize_rich_editor', 'wp_kses_post' );
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	function get_registered_settings() {
		/**
		 * Fires before attempting to retrieve registered settings.
		 *
		 * @since 1.0.2
		 *
		 * @param Settings $this Settings instance.
		 */
		do_action( 'eaccounting_pre_get_registered_settings', $this );

		$settings = array(
			'general' => apply_filters(
				'eaccounting_settings_general',
				array(
					'company_settings'           => array(
						'name' => __( 'Company Settings', 'wp-ever-accounting' ),
						'desc' => '',
						'type' => 'header',
					),
					'company_name'               => array(
						'name' => __( 'Name', 'wp-ever-accounting' ),
						'type' => 'text',
						'tip'  => 'XYZ Company',
						'attr' => array(
							'required'    => 'required',
							'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
						),
					),
					'company_email'              => array(
						'name'              => __( 'Email', 'wp-ever-accounting' ),
						'type'              => 'text',
						'std'               => get_option( 'admin_email' ),
						'sanitize_callback' => 'sanitize_email',
					),
					'company_phone'              => array(
						'name' => __( 'Phone Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_tax_number'         => array(
						'name' => __( 'Tax Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_city'               => array(
						'name' => __( 'City', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_address'            => array(
						'name' => __( 'Address', 'wp-ever-accounting' ),
						'type' => 'textarea',
					),
					'company_state'              => array(
						'name' => __( 'State', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_postcode'           => array(
						'name' => __( 'Postcode', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_country'            => array(
						'name'    => __( 'Country', 'wp-ever-accounting' ),
						'type'    => 'select',
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select Country', 'wp-ever-accounting' ) ) + eaccounting_get_countries(),
					),
					'company_logo'               => array(
						'name' => __( 'Logo', 'wp-ever-accounting' ),
						'type' => 'upload',
					),
					'local_settings'             => array(
						'name' => '<strong>' . __( 'Localisation Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header',
					),
					'financial_year_start'       => array(
						'name'  => __( 'Financial Year Start', 'wp-ever-accounting' ),
						'std'   => '01-01',
						'class' => 'ea-financial-start',
						'type'  => 'text',
					),
					'default_settings'           => array(
						'name' => '<strong>' . __( 'Default Settings', 'wp-ever-accounting' ) . '</strong>',
						'desc' => '',
						'type' => 'header',
					),
					//                  'default_account'        => array(
					//                      'name'    => __( 'Account', 'wp-ever-accounting' ),
					//                      'type'    => 'select',
					//                      'class'   => 'ea-select2',
					//                      'options' => array( '' => __( 'Select default account', 'wp-ever-accounting' ) ) + wp_list_pluck( $accounts, 'name', 'id' ),
					//                      'attr'    => array(
					//                          'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
					//                      ),
					//                  ),
					//                  'default_currency'       => array(
					//                      'name'     => __( 'Currency', 'wp-ever-accounting' ),
					//                      'type'     => 'select',
					//                      // 'std'     => 'USD',
					//                      'desc' => __( 'Default currency rate will update to 1', 'wp-ever-accounting' ),
					//                      'class'    => 'ea-select2',
					//                      'options'  => array( '' => __( 'Select default currency', 'wp-ever-accounting' ) ) + wp_list_pluck( $currencies, 'name', 'code' ),
					//                      'attr'     => array(
					//                          'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
					//                      ),
					//                  ),
						'default_payment_method' => array(
							'name'    => __( 'Payment Method', 'wp-ever-accounting' ),
							'std'     => 'cash',
							'type'    => 'select',
							'options' => eaccounting_get_payment_methods(),
						),
					'invoice_prefix'             => array(
						'name'    => __( 'Invoice Prefix', 'wp-ever-accounting' ),
						'std'     => 'INV-',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_digit'              => array(
						'name'    => __( 'Minimum Digits', 'wp-ever-accounting' ),
						'std'     => '5',
						'type'    => 'number',
						'section' => 'invoice',
					),
					'invoice_title'              => array(
						'name'    => __( 'Invoice Title', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_subheading'         => array(
						'name'    => __( 'Invoice Subheading', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_notes'              => array(
						'name'    => __( 'Invoice Notes', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'invoice',
					),
					'invoice_footer'             => array(
						'name'    => __( 'Invoice Footer', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'invoice',
					),
					'invoice_item_label'         => array(
						'name'    => __( 'Item Label', 'wp-ever-accounting' ),
						'std'     => __( 'Item', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_price_label'        => array(
						'name'    => __( 'Price Label', 'wp-ever-accounting' ),
						'std'     => __( 'Price', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_quantity_label'     => array(
						'name'    => __( 'Quantity Label', 'wp-ever-accounting' ),
						'std'     => __( 'Quantity', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'bill_prefix'                => array(
						'name'    => __( 'Bill Prefix', 'wp-ever-accounting' ),
						'std'     => 'BILL-',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_digit'                 => array(
						'name'    => __( 'Bill Digits', 'wp-ever-accounting' ),
						'std'     => '5',
						'type'    => 'number',
						'section' => 'bill',
					),
					'bill_title'                 => array(
						'name'    => __( 'Bill Title', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_subheading'            => array(
						'name'    => __( 'Bill Subheading', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_notes'                 => array(
						'name'    => __( 'Bill Notes', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'bill',
					),
					'bill_footer'                => array(
						'name'    => __( 'Bill Footer', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'bill',
					),
					'bill_item_label'            => array(
						'name'    => __( 'Item Label', 'wp-ever-accounting' ),
						'std'     => __( 'Item', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_price_label'           => array(
						'name'    => __( 'Price Label', 'wp-ever-accounting' ),
						'std'     => __( 'Price', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_quantity_label'        => array(
						'name'    => __( 'Quantity Label', 'wp-ever-accounting' ),
						'std'     => __( 'Quantity', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
				)
			),
			'taxes'   => apply_filters(
				'eaccounting_settings_taxes',
				array(
					'tax_settings'          => array(
						'name' => __( 'Tax Settings', 'wp-ever-accounting' ),
						'desc' => '',
						'type' => 'header',
					),
					'enable_taxes'          => array(
						'name' => __( 'Enable Taxes', 'wp-ever-accounting' ),
						'type' => 'checkbox',
						'std'  => '1',
						'desc' => __( 'Enable tax rates and calculations.', 'wp-ever-accounting' ),
					),
					'tax_subtotal_rounding' => array(
						'name' => __( 'Rounding', 'wp-ever-accounting' ),
						'type' => 'checkbox',
						'desc' => __( 'Round tax at subtotal level, instead of rounding per tax rate.', 'wp-ever-accounting' ),
					),
					'prices_include_tax'    => array(
						'name'    => __( 'Prices entered with tax', 'wp-ever-accounting' ),
						'type'    => 'select',
						'std'     => 'yes',
						'options' => array(
							'yes' => __( 'Yes, I will enter prices inclusive of tax', 'wp-ever-accounting' ),
							'no'  => __( 'No, I will enter prices exclusive of tax', 'wp-ever-accounting' ),
						),
					),
					'tax_display_totals'    => array(
						'name'    => __( 'Display tax totals	', 'wp-ever-accounting' ),
						'type'    => 'select',
						'std'     => 'total',
						'options' => array(
							'total'      => __( 'As a single total', 'wp-ever-accounting' ),
							'individual' => __( 'As individual tax rates', 'wp-ever-accounting' ),
						),
					),
				)
			),
			'emails'  => apply_filters(
				'eaccounting_settings_emails',
				array()
			),
		);

		/**
		 * Filters the entire default settings array.
		 * add_filter( 'eaccounting_settings', function( $settings ){
		 *
		 * } )
		 *
		 * @since 1.0.2
		 *
		 * @param array $settings Array of default settings.
		 */
		$settings = apply_filters( 'eaccounting_settings', $settings );

		$registered = array();
		foreach ( $settings as $tab => $options ) {
			foreach ( $options as $key => $option ) {
				$registered[ $key ] = wp_parse_args(
					$option,
					array(
						'section' => 'main',
						'tab'     => $tab,
					)
				);
			}
		}

		return $registered;
	}

	/**
	 * Add email settings.
	 *
	 * @since 1.1.0
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function register_email_settings( $settings ) {
		$email_settings = array(
			'default_settings'          => array(
				'name' => __( 'Email sender options', 'wp-ever-accounting' ),
				'desc' => '',
				'type' => 'header',
			),
			'email_from_name'           => array(
				'name' => __( 'From Name', 'wp-ever-accounting' ),
				'std'  => site_url(),
				'type' => 'text',
			),
			'email_from'                => array(
				'name' => __( 'From Email', 'wp-ever-accounting' ),
				'std'  => get_option( 'admin_email' ),
				'type' => 'text',
			),
			'admin_email'               => array(
				'name' => __( 'Admin Email', 'wp-ever-accounting' ),
				'std'  => get_option( 'admin_email' ),
				'type' => 'text',
			),
			'email_sections_title'      => array(
				'name' => __( 'Email notifications', 'wp-ever-accounting' ),
				'desc' => __( 'Email notifications sent from Ever Accounting are listed below. Click on an email to configure it.', 'wp-ever-accounting' ),
				'type' => 'header',
			),
			'email_sections'            => array(
				'type'     => '',
				'callback' => array( $this, 'email_sections' ),
			),
			'email_new_invoice_header'  => array(
				'name'    => __( 'New Invoice', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the site admin whenever there is a new invoice.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'new_invoice',
			),
			'email_new_invoice_active'  => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'new_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_new_invoice_subject' => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'new_invoice',
				'std'     => __( '[{site_title}] New Invoice created #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_new_invoice_heading' => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'new_invoice',
				'std'     => __( 'New Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_new_invoice_body'    => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'new_invoice',
				'std'     => __( 'New invoice has been created for the customer {customer_name} with a total of {invoice_total}, <a href="{invoice_admin_url}">View</a> invoice.', 'wp-ever-accounting' ),
			),
			'email_new_invoice_tags'    => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'new_invoice',
				'class'   => 'email-tags',
				'html'    => 'We sent your invoice ({invoice_number}) to {name} for {invoice_total} {invoice_currency}.',
			),
		);

		return array_merge( $settings, $email_settings );
	}

	/**
	 * Get settings tabs.
	 *
	 * @since 1.1.0
	 * @return array list of tabs.
	 */
	protected static function get_tabs() {
		return apply_filters(
			'eaccounting_settings_tabs',
			array(
				'general'    => __( 'General', 'wp-ever-accounting' ),
				'currencies' => __( 'Currencies', 'wp-ever-accounting' ),
				'categories' => __( 'Categories', 'wp-ever-accounting' ),
				'taxes'      => __( 'Taxes', 'wp-ever-accounting' ),
				'advanced'   => __( 'Advanced', 'wp-ever-accounting' ),
				'emails'     => __( 'Emails', 'wp-ever-accounting' ),
				'misc'       => __( 'Misc', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get settings tabs.
	 *
	 * @since 1.1.0
	 * @return array list of sections.
	 */
	protected static function get_sections() {
		$sections = array();
		$defaults = array(
			'general'  => array(
				'main'    => __( 'General Settings', 'wp-ever-accounting' ),
				'invoice' => __( 'Invoice Settings', 'wp-ever-accounting' ),
				'bill'    => __( 'Bill Settings', 'wp-ever-accounting' ),
			),
			'taxes'    => array(
				'main'  => __( 'Tax Settings', 'wp-ever-accounting' ),
				'rates' => __( 'Tax Rates', 'wp-ever-accounting' ),
			),
			'advanced' => array(
				'keys' => __( 'REST API', 'wp-ever-accounting' ),
			),
		);

		foreach ( self::get_tabs() as $tab_id => $tab_label ) {
			$sections[ $tab_id ] = apply_filters( 'eaccounting_settings_tab_sections_' . $tab_id, isset( $defaults[ $tab_id ] ) ? $defaults[ $tab_id ] : array( 'main' => '' ) );
		}

		return $sections;
	}

	/**
	 * Add all settings sections and fields
	 *
	 * @since 1.0.2
	 * @return void
	 */
	function register_settings() {
		$options  = $this->get_registered_settings();
		$settings = array();

		foreach ( $options as $key => $option ) {
			if ( ! isset( $settings[ $option['tab'] ] ) ) {
				$settings[ $option['tab'] ] = array();
			}

			if ( ! isset( $settings[ $option['tab'] ] [ $option['section'] ] ) ) {
				add_settings_section(
					$option['section'],
					__return_null(),
					'__return_false',
					'eaccounting_settings_' . $option['tab']
				);
			}

			$title    = isset( $option['name'] ) ? $option['name'] : '';
			$callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );
			$tip      = isset( $option['tip'] ) ? eaccounting_help_tip( $option['tip'] ) : '';

			if ( ! in_array( $option['type'], array( 'checkbox', 'multicheck', 'radio', 'header' ), true ) ) {
				$title = sprintf( '<label for="eaccounting_settings[%1$s]">%2$s</label>%3$s', $key, $title, $tip );
			} elseif ( 'header' === $option['type'] ) {
				$title = sprintf( '<h3>%s</h3>', esc_html( $title ) );
			}

			add_settings_field(
				'eaccounting_settings[' . $key . ']',
				$title,
				is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
				'eaccounting_settings_' . $option['tab'],
				$option['section'],
				array(
					'id'          => $key,
					'section'     => $option['section'],
					'desc'        => ! empty( $option['desc'] ) ? $option['desc'] : '',
					'name'        => isset( $option['name'] ) ? $option['name'] : null,
					'size'        => isset( $option['size'] ) ? $option['size'] : null,
					'max'         => isset( $option['max'] ) ? $option['max'] : null,
					'min'         => isset( $option['min'] ) ? $option['min'] : null,
					'step'        => isset( $option['step'] ) ? $option['step'] : null,
					'options'     => isset( $option['options'] ) ? $option['options'] : array(),
					'attr'        => isset( $option['attr'] ) ? $option['attr'] : array(),
					'std'         => isset( $option['std'] ) ? $option['std'] : '',
					'disabled'    => isset( $option['disabled'] ) ? $option['disabled'] : '',
					'class'       => isset( $option['wrap_class'] ) ? $option['wrap_class'] : '',
					'input_class' => isset( $option['class'] ) ? $option['class'] : '',
					'placeholder' => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
					'style'       => isset( $option['style'] ) ? $option['style'] : '',
					'html'        => isset( $option['html'] ) ? $option['html'] : '',
				)
			);
		}
		register_setting( 'eaccounting_settings', 'eaccounting_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Add emails sections
	 *
	 * @since 1.1.0
	 *
	 * @param $args
	 */
	function email_sections( $args ) {
		$notifications = apply_filters(
			'eaccounting_email_notifications',
			array(
				'new_invoice'           => __( 'New Invoice', 'wp-ever-accounting' ),
				'cancelled_invoice'     => __( 'Cancelled Invoice', 'wp-ever-accounting' ),
				'failed_invoice'        => __( 'Failed Invoice', 'wp-ever-accounting' ),
				'customer_invoice'      => __( 'Customer Invoice', 'wp-ever-accounting' ),
				'customer_invoice_note' => __( 'Customer Note', 'wp-ever-accounting' ),
			)
		);
		?>
		<table class="form-table widefat ea-emails">
			<thead>
			<tr>
				<th class="ea-emails-email"><?php echo _e( 'Email', 'wp-ever-accounting' ); ?></th>
				<th class="ea-emails-status"><?php echo _e( 'Status', 'wp-ever-accounting' ); ?></th>
				<th class="ea-emails-manage"><?php echo _e( 'Manage', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $notifications as $key => $title ) : ?>
				<tr>
					<td>
						<?php
						echo sprintf(
							'<a href="%s"><strong>%s</strong></a>',
							esc_url(
								add_query_arg(
									array(
										'page'    => 'ea-settings',
										'tab'     => 'emails',
										'section' => $key,

									)
								)
							),
							esc_html( $title )
						);
						?>
					</td>
					<td><?php echo sprintf( '<span class="email-status %s"><span class="dashicons dashicons-yes-alt">&nbsp;</span></span>', $this->get( 'email_' . $key . '_active' ) === 'yes' ? 'active' : 'inactive' ); ?> </td>
					<td>
						<?php
						echo sprintf(
							'<a href="%s" class="button button-secondary">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'page'    => 'ea-settings',
										'tab'     => 'emails',
										'section' => $key,

									)
								)
							),
							__( 'Manage', 'wp-ever-accounting' )
						);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function header_callback( $args ) {
		if ( ! empty( $args['desc'] ) ) {
			echo $args['desc'];
		}
	}

	/**
	 * Text Callback
	 *
	 * Renders text fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function text_callback( $args ) {
		$default = isset( $args['std'] ) ? $args['std'] : '';
		$value   = $this->get( $args['id'], $default );

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="text" class="%1$s-text %2$s" style="%3$s" name="eaccounting_settings[%4$s]" id="eaccounting_settings[%4$s]" value="%5$s" %6$s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function checkbox_callback( $args ) {
		$value      = $this->get( $args['id'] );
		$checked    = isset( $value ) ? checked( 'yes', $value, false ) : '';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$id         = 'eaccounting_settings[' . $args['id'] . ']';
		$html       = '<label for="' . $id . '">';
		$html      .= '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="yes" ' . $checked . ' ' . $attributes . '/>&nbsp;';
		$html      .= $args['desc'];
		$html      .= '</label>';

		echo $html;
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function multicheck_callback( $args ) {

		if ( ! empty( $args['options'] ) ) {
			foreach ( $args['options'] as $key => $option ) {
				if ( isset( $this->settings[ $args['id'] ][ $key ] ) ) {
					$enabled = $option;
				} else {
					$enabled = null;
				}
				echo '<label for="eaccounting_settings[' . $args['id'] . '][' . $key . ']">';
				echo '<input name="eaccounting_settings[' . $args['id'] . '][' . $key . ']" id="eaccounting_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';
				echo $option . '</label><br/>';
			}
			echo '<p class="description">' . $args['desc'] . '</p>';
		}
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function radio_callback( $args ) {

		echo '<fieldset id="eaccounting_settings[' . $args['id'] . ']">';
		echo '<legend class="screen-reader-text">' . $args['name'] . '</legend>';

		foreach ( $args['options'] as $key => $option ) :
			$checked = false;

			if ( isset( $this->settings[ $args['id'] ] ) && $this->settings[ $args['id'] ] == $key ) { //phpcs:ignore
				$checked = true;
			} elseif ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $this->options[ $args['id'] ] ) ) { //phpcs:ignore
				$checked = true;
			}

			echo '<label for="eaccounting_settings[' . $args['id'] . '][' . $key . ']">';
			echo '<input name="eaccounting_settings[' . $args['id'] . ']" id="eaccounting_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>';
			echo $option . '</label><br/>';
		endforeach;

		echo '</fieldset><p class="description">' . $args['desc'] . '</p>';
	}

	/**
	 * URL Callback
	 *
	 * Renders URL fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function url_callback( $args ) {

		if ( isset( $this->settings[ $args['id'] ] ) && ! empty( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="url" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}


	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function number_callback( $args ) {

		// Get value, with special consideration for 0 values, and never allowing negative values
		$value = isset( $this->settings[ $args['id'] ] ) ? $this->settings[ $args['id'] ] : null;
		$value = ( ! is_null( $value ) && '' !== $value && floatval( $value ) >= 0 ) ? floatval( $value ) : null;

		// Saving the field empty will revert to std value, if it exists
		$std   = ( isset( $args['std'] ) && ! is_null( $args['std'] ) && '' !== $args['std'] && floatval( $args['std'] ) >= 0 ) ? $args['std'] : null;
		$value = ! is_null( $value ) ? $value : ( ! is_null( $std ) ? $std : null );
		$value = eaccounting_round_number( $value );

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="number" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function textarea_callback( $args ) {

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<textarea type="text" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" %s>%s</textarea>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			$attributes,
			esc_textarea( stripslashes( $value ) )
		);
		$html .= $desc;

		echo $html;

	}

	/**
	 * Password Callback
	 *
	 * Renders password fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function password_callback( $args ) {

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="password" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= $desc;

		echo $html;
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0.2
	 * @global      $this ->options Array of all the EverAccounting Options
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function select_callback( $args ) {

		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html = sprintf(
			'<select class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" %s>',
			$size,
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			$attributes
		);

		foreach ( $args['options'] as $key => $option_value ) {
			$html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), eaccounting_selected( esc_attr( $key ), esc_attr( $value ) ), esc_html( $option_value ) );
		}

		$html .= '</select>';
		$html .= $desc;

		echo $html;
	}

	/**
	 * Rich Editor Callback
	 *
	 * Renders rich editor fields.
	 *
	 * @since 1.0.2
	 * @global        $this       ->options Array of all the EverAccounting Options
	 * @global string $wp_version WordPress Version
	 *
	 * @param array   $args       Arguments passed by the setting
	 */
	function rich_editor_callback( $args ) {

		if ( !empty( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		ob_start();
		wp_editor( stripslashes( $value ), 'eaccounting_settings_' . $args['id'], array( 'textarea_name' => 'eaccounting_settings[' . $args['id'] . ']' ) );
		$html = ob_get_clean();

		$html .= '<br/><p class="description"> ' . $args['desc'] . '</p>';

		echo $html;
	}

	/**
	 * Upload Callback
	 *
	 * Renders file upload fields.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguements passed by the setting
	 */
	function upload_callback( $args ) {
		if ( isset( $this->settings[ $args['id'] ] ) ) {
			$value = $this->settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$attributes = eaccounting_implode_html_attributes( $args['attr'] );
		$desc       = ! empty( $args['desc'] ) ? sprintf( '<p class="description">%s</p>', wp_kses_post( $args['desc'] ) ) : '';

		$html  = sprintf(
			'<input type="text" class="%s-text %s" style="%s" name="eaccounting_settings[%s]" id="eaccounting_settings[%s]" value="%s" %s/>',
			esc_attr( $size ),
			esc_attr( $args['input_class'] ),
			esc_attr( $args['style'] ),
			esc_attr( $args['id'] ),
			esc_attr( $args['id'] ),
			esc_attr( stripslashes( $value ) ),
			$attributes
		);
		$html .= sprintf( '<span>&nbsp;<input type="button" class="ea_settings_upload_button button-secondary" value="%s"/></span>', __( 'Upload File', 'wp-ever-accounting' ) );
		$html .= $desc;

		echo $html;
	}


	function html_callback( $args ) {
		$args = wp_parse_args( $args, array( 'html' => '' ) );
		echo sprintf( '<div class="ea-settings-html %s">%s</div>', sanitize_html_class( $args['input_class'] ), wp_kses_post( $args['html'] ) );
	}

	/**
	 * Missing Callback
	 *
	 * If a function is missing for settings callbacks alert the user.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args Arguments passed by the setting
	 *
	 * @return void
	 */
	function missing_callback( $args ) {
		/* translators: %s name of the callback */
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-ever-accounting' ), $args['id'] );
	}

	/**
	 * Get the value of a specific setting
	 *
	 * Note: By default, zero values are not allowed. If you have a custom
	 * setting that needs to allow 0 as a valid value, but sure to add its
	 * key to the filtered array seen in this method.
	 *
	 * @since  1.0.2
	 *
	 * @param mixed  $default (optional)
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function get( $key, $default = false ) {

		// Only allow non-empty values, otherwise fallback to the default
		$value = ! empty( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;

		$zero_values_allowed = array();

		/**
		 * Filters settings allowed to accept 0 as a valid value without
		 * falling back to the default.
		 *
		 * @param array $zero_values_allowed Array of setting IDs.
		 */
		$zero_values_allowed = (array) apply_filters( 'eaccounting_settings_zero_values_allowed', $zero_values_allowed );

		// Allow 0 values for specified keys only
		if ( in_array( $key, $zero_values_allowed ) ) { // phpcs:ignore

			$value = isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : null;
			$value = ( ! is_null( $value ) && '' !== $value ) ? $value : $default;

		}

		return $value;
	}

	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	function sanitize_settings( $input = array() ) {
		if ( empty( $_POST['_wp_http_referer'] ) ) {
			return $input;
		}

		parse_str( $_POST['_wp_http_referer'], $referrer );

		$saved = get_option( 'eaccounting_settings', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$settings = $this->get_registered_settings();
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
		$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';
		$settings = isset( $settings[ $tab ] ) ? wp_list_filter( $settings[ $tab ], array( 'section' => $section ) ) : array();

		$input = $input ? $input : array();

		// Ensure a value is always passed for every checkbox
		if ( ! empty( $settings ) ) {

			foreach ( $settings as $key => $setting ) {

				// Single checkbox
				if ( 'checkbox' === $setting['type'] ) {
					$input[ $key ] = ! empty( $input[ $key ] );
				}

				// Multicheck list
				if ( 'multicheck' === $settings[ $key ]['type'] ) {
					if ( empty( $input[ $key ] ) ) {
						$input[ $key ] = array();
					}
				}
			}
		}

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type              = isset( $settings[ $key ]['type'] ) ? $settings[ $key ]['type'] : false;
			$sanitize_callback = isset( $settings[ $key ]['sanitize_callback'] ) ? $settings[ $key ]['sanitize_callback'] : false;
			$input[ $key ]     = $value;

			if ( $type ) {
				/**
				 * Filters the sanitized value for a setting of a given type.
				 *
				 * This filter is appended with the setting type (checkbox, select, etc), for example:
				 *
				 *     `eaccounting_settings_sanitize_checkbox`
				 *     `eaccounting_settings_sanitize_select`
				 *
				 * @since 1.0.2
				 *
				 * @param string $key   The settings key.
				 *
				 * @param array  $value The input array and settings key defined within.
				 */
				$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize_' . $type, $input[ $key ], $key );

				if ( $sanitize_callback && is_callable( $sanitize_callback ) ) {
					$input[ $key ] = call_user_func( $sanitize_callback, $value );
				}
			}

			/**
			 * General setting sanitization filter
			 *
			 * @since 1.0
			 *
			 * @param string $key   The settings key.
			 *
			 * @param array  $input [ $key ] The input array and settings key defined within.
			 */
			$input[ $key ] = apply_filters( 'eaccounting_settings_sanitize', $input[ $key ], $key );
		}

		add_settings_error( 'eaccounting-notices', '', __( 'Settings updated.', 'wp-ever-accounting' ), 'updated' );

		return array_merge( $saved, $input );
	}

	/**
	 * Sets an option (in memory).
	 *
	 * @since  1.0.2
	 * @access public
	 *
	 * @param bool  $save     Optional. Whether to trigger saving the option or options. Default false.
	 *
	 * @param array $settings An array of `key => value` setting pairs to set.
	 *
	 * @return bool If `$save` is not false, whether the options were saved successfully. True otherwise.
	 */
	public function set( $settings, $save = false ) {
		foreach ( $settings as $option => $value ) {
			$this->settings[ $option ] = $value;
		}

		if ( false !== $save ) {
			return $this->save();
		}

		return true;
	}

	/**
	 * Saves option values queued in memory.
	 *
	 * Note: If posting separately from the main settings submission process, this method should
	 * be called directly for direct saving to prevent memory pollution. Otherwise, this method
	 * is only accessible via the optional `$save` parameter in the set() method.
	 *
	 * @since 1.0.2
	 *
	 * @param array $options Optional. Options to save/overwrite directly. Default empty array.
	 *
	 * @return bool False if the options were not updated (saved) successfully, true otherwise.
	 */
	protected function save( $options = array() ) {
		$all_options = $this->get_all();

		if ( ! empty( $options ) ) {
			$all_options = array_merge( $all_options, $options );
		}

		$updated = update_option( 'eaccounting_settings', $all_options );

		// Refresh the options array available in memory (prevents unexpected race conditions).
		$this->settings = get_option( 'eaccounting_settings', array() );

		return $updated;
	}

	/**
	 * Get all settings
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_all() {
		return $this->settings;
	}

	/**
	 * Get sections for a specific tab.
	 *
	 * @since 1.1.0
	 *
	 * @param $tab
	 *
	 * @return array|mixed
	 */
	protected static function get_tab_sections( $tab ) {
		$sections = self::get_sections();

		return array_key_exists( $tab, $sections ) ? $sections[ $tab ] : array();
	}

	/**
	 * Output settings Page
	 *
	 * @since 1.1.0
	 */
	public static function output() {
		$tabs = self::get_tabs();
		// Get current tab/section.
		$current_tab       = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
		$current_section   = empty( $_REQUEST['section'] ) ? 'main' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.
		$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';
		$tab_sections      = self::get_tab_sections( $current_tab );

		$tab_exists = isset( $tabs[ $current_tab ] ) || has_action( 'eaccounting_settings_' . $current_tab ) || has_action( 'eaccounting_settings_tabs_' . $current_tab );
		if ( ! $tab_exists ) {
			wp_safe_redirect( admin_url( 'admin.php?page=ea-settings' ) );
			exit;
		}
		ob_start();
		?>
		<div class="wrap eaccounting-settings">

			<?php self::render_tabs( $tabs, $current_tab ); ?>
			<?php self::render_subsub( $tab_sections, $current_tab, $current_section ); ?>
			<h1 class="screen-reader-text"><?php echo esc_html( $current_tab_label ); ?></h1>

			<?php
			if ( has_action( 'eaccounting_settings_tab_' . $current_tab ) ) {
				do_action( 'eaccounting_settings_tab_' . $current_tab );
			} elseif ( has_action( 'eaccounting_settings_tab_' . $current_tab . '_section_' . $current_section ) ) {
				do_action( 'eaccounting_settings_tab_' . $current_tab . '_section_' . $current_section );
			} else {
				?>
				<form method="post" id="mainform" action="options.php" enctype="multipart/form-data">
					<table class="form-table">
						<?php
						settings_errors();
						settings_fields( 'eaccounting_settings' );
						do_settings_fields( 'eaccounting_settings_' . $current_tab, $current_section );
						?>
					</table>

					<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
						<?php submit_button(); ?>
					<?php endif; ?>
				</form>
				<?php
			}

			?>

		</div><!-- .wrap -->
		<?php
		echo ob_get_clean();
	}

	/**
	 * Render tabbed nav.
	 *
	 * @since 1.1.0
	 *
	 * @param string $current_tab
	 * @param        $tabs
	 */
	public static function render_tabs( $tabs, $current_tab = 'general' ) {
		if ( empty( $tabs ) ) {
			return;
		}
		echo '<nav class="nav-tab-wrapper">';
		foreach ( $tabs as $slug => $label ) {
			echo '<a href="' . esc_html( admin_url( 'admin.php?page=ea-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
		}
		do_action( 'eaccounting_settings_tabs' );
		echo '</nav>';
	}

	/**
	 * Render section's subsub.
	 *
	 * @since 1.1.0
	 *
	 * @param string $current_tab
	 * @param string $current_section
	 * @param        $sections
	 */
	public static function render_subsub( $sections, $current_tab = 'general', $current_section = 'main' ) {
		if ( empty( $sections ) || sizeof( $sections ) < 2 ) {
			return;
		}
		$section_keys = array_keys( $sections );
		echo '<ul class="subsubsub">';
		foreach ( $sections as $id => $label ) {
			$url   = admin_url( 'admin.php?page=ea-settings&tab=' . $current_tab . '&section=' . $id );
			$class = $current_section == $id ? 'current' : ''; //phpcs:ignore
			echo sprintf( '<li><a href="%s" class="%s">%s</a> %s </li>', $url, $class, $label, end( $section_keys ) === $id ? '' : '|' );
		}
		echo '</ul><br class="clear"/>';
	}


}
