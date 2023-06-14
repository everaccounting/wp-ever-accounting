<h1>Demo elements</h1>

<?php
eac_input_fields(
	array(
		array(
			'id'          => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'value'       => 'lorem ipsum',
			'type'        => 'wysiwyg',
			'desc'        => __( 'Enter item name', 'wp-ever-accounting' ),
			'tooltip'     => __( 'Enter item name', 'wp-ever-accounting' ),
			'required'    => true,
			'placeholder' => 'Enter name',
			'prefix'      => 'Prefix',
			'suffix'      => 'Suffix',
		),
		array(
			'id'       => 'name',
			'label'    => __( 'Name', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'textarea',
			'desc'     => __( 'Enter item name', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item name', 'wp-ever-accounting' ),
			'required' => true,
			'prefix'   => 'Prefix',
			'suffix'   => 'Suffix',
			'class'    => 'eac-col-12',
		),
		array(
			'id'       => 'description',
			'label'    => __( 'Description', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'country',
			'desc'     => __( 'Enter item description', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item description', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'prefix'   => 'Prefix',
			'suffix'   => 'Suffix',
		),
		// currency.
		array(
			'id'       => 'currency',
			'label'    => __( 'Currency', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'currency',
			'desc'     => __( 'Enter item currency', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item currency', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'prefix'   => 'Prefix',
			'suffix'   => 'Suffix',
		),
		// account.
		array(
			'id'       => 'account',
			'label'    => __( 'Account', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'account',
			'desc'     => __( 'Enter item account', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item account', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'prefix'   => 'Prefix',
			'suffix'   => 'Suffix',
		),
		// customer.
		array(
			'id'       => 'customer',
			'label'    => __( 'Customer', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'customer',
			'desc'     => __( 'Enter item customer', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item customer', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'suffix'   => 'Suffix',
		),
		array(
			'id'       => 'customer',
			'label'    => __( 'Customer', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'customer',
			'desc'     => __( 'Enter item customer', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item customer', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'suffix'   => sprintf(
				'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
				esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
				__( 'Add Category', 'wp-ever-accounting' )
			),
		),
		array(
			'id'       => 'customer',
			'label'    => __( 'Customer', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'customer',
			'desc'     => __( 'Enter item customer', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item customer', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'suffix'   => sprintf(
				'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
				esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
				__( 'Add Category', 'wp-ever-accounting' )
			),
			'prefix'   => sprintf(
				'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
				esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
				__( 'Add Category', 'wp-ever-accounting' )
			),
		),
		// vendor.
		array(
			'id'       => 'vendor',
			'label'    => __( 'Vendor', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'select',
			'subtype'  => 'vendor',
			'desc'     => __( 'Enter item vendor', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item vendor', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'prefix'   => 'Prefix',
		),
		// category.
		array(
			'id'         => 'category',
			'label'      => __( 'Category', 'wp-ever-accounting' ),
			'value'      => '',
			'type'       => 'select',
			'subtype'    => 'category',
			'query_args' => 'type=item',
			'desc'       => __( 'Enter item category', 'wp-ever-accounting' ),
			'tooltip'    => __( 'Enter item category', 'wp-ever-accounting' ),
			'required'   => false,
			'class'      => 'eac-col-12',
		),
		// radio.
		array(
			'id'       => 'radio',
			'label'    => __( 'Radio', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'radio',
			'desc'     => __( 'Enter item radio', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item radio', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'options'  => array(
				'option1' => 'Option 1',
				'option2' => 'Option 2',
				'option3' => 'Option 3',
			),
		),
		// checkbox.
		array(
			'id'       => 'checkbox',
			'label'    => __( 'Checkbox', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'checkbox',
			'desc'     => __( 'Enter item checkbox', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item checkbox', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'options'  => array(
				'option1' => 'Option 1',
				'option2' => 'Option 2',
				'option3' => 'Option 3',
			),
		),
		// checkboxes.
		array(
			'id'       => 'checkboxes',
			'label'    => __( 'Checkboxes', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'checkboxes',
			'desc'     => __( 'Enter item checkboxes', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item checkboxes', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'options'  => array(
				'option1' => 'Option 1',
				'option2' => 'Option 2',
				'option3' => 'Option 3',
			),
		),
		// switch.
		array(
			'id'       => 'switch',
			'label'    => __( 'Switch', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'switch',
			'desc'     => __( 'Enter item switch', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item switch', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		// radio_group.
		array(
			'id'       => 'radio_group',
			'label'    => __( 'Radio Group', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'radio_group',
			'desc'     => __( 'Enter item radio_group', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item radio_group', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'options'  => array(
				'option1' => 'Option 1',
				'option2' => 'Option 2',
				'option3' => 'Option 3',
			),
		),
		// checkboxes.
		array(
			'id'       => 'checkboxes',
			'label'    => __( 'Checkboxes', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'checkboxes',
			'desc'     => __( 'Enter item checkboxes', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item checkboxes', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
			'options'  => array(
				'option1' => 'Option 1',
				'option2' => 'Option 2',
				'option3' => 'Option 3',
			),
		),
		// date.
		array(
			'id'       => 'date',
			'label'    => __( 'Date', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'date',
			'desc'     => __( 'Enter item date', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item date', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		// date_range.
		array(
			'id'       => 'date_range',
			'label'    => __( 'Date Range', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'date_range',
			'desc'     => __( 'Enter item date_range', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item date_range', 'wp-ever-accounting' ),
			'required' => false,
			'wrapper'  => false,
			'class'    => 'eac-col-12',
			'prefix'   => 'Prefix',
			'suffix'   => 'Suffix',
		),
		// file.
		array(
			'id'       => 'file',
			'label'    => __( 'File', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'file',
			'desc'     => __( 'Enter item file', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item file', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		// money.
		array(
			'id'       => 'money',
			'label'    => __( 'Money', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'money',
			'desc'     => __( 'Enter item money', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item money', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		// hidden.
		array(
			'id'       => 'hidden',
			'label'    => __( 'Hidden', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'hidden',
			'desc'     => __( 'Enter item hidden', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item hidden', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		// name.
		array(
			'id'       => 'name',
			'label'    => __( 'Name', 'wp-ever-accounting' ),
			'value'    => '',
			'type'     => 'text',
			'desc'     => __( 'Enter item name', 'wp-ever-accounting' ),
			'tooltip'  => __( 'Enter item name', 'wp-ever-accounting' ),
			'required' => false,
			'class'    => 'eac-col-12',
		),
		array(
			'id'                           => 'rate',
			'label'                        => __( 'Rate', 'wp-ever-accounting' ),
			'type'                         => 'number',
			'value'                        => 1,
			// 'class'    => 'eac-col-12',
								'required' => true,
			// translators: %s is the base currency.
			'prefix'                       => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
			'suffix'                       => 'BDT',
		),
	)
)
?>
Testing alpine js form.
<div x-data="{text: 'hello'}">
	<input type="text" x-model="text">
	<p x-text="text"></p>
</div>
