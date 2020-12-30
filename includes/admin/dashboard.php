<?php

add_action( 'admin_init',  'example_wp_dashboard_setup' );

/**
 * Remove core widhets and add widget to Dashboard in third column.
 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function example_wp_dashboard_setup() {

	// Add custom dashbboard widget.
	add_meta_box( 'dashboard_widget_example',
			__( 'Example Widget', 'example-text-domain' ),
			'render_example_widget',
			'affiliates_page_affiliate-wp-reports',
			'primary',  // $context: 'advanced', 'normal', 'side', 'column3', 'column4'
			'high'     // $priority: 'high', 'core', 'default', 'low'

	);
}

/**
 * Render widget.
 */
function render_example_widget() {
	?>
	<p>Do something.</p>
	<?php
}
