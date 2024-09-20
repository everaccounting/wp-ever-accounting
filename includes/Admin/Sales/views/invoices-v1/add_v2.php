<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $document \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">
	<div class="eac-poststuff">
		<div class="column-1"></div>

		<div class="column-2">
			<?php
			eac_form_field(
				array(
					'label'            => __( 'Currency', 'wp-ever-accounting' ),
					'type'             => 'select',
					'id'               => 'currency_code',
					'value'            => $document->currency_code,
					'default'          => eac_base_currency(),
					'class'            => 'eac_select2',
					'options'          => array( $document->currency ),
					'option_value'     => 'code',
					'option_label'     => 'formatted_name',
					'data-action'      => 'eac_json_search',
					'data-type'        => 'currency',
					'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
				)
			);
			eac_form_field(
				array(
					'label' => __( 'Discount', 'wp-ever-accounting' ),
				)
			);
			?>
		</div><!-- .column-2 -->
	</div>
</form>
