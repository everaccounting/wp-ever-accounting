<?php
/**
 * Edit customer view.
 * Page: Sales
 * Tab: Customers
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $customer Customer Customer object.
 */

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$customer = Customer::make( $id );

?>
<h1 class="wp-heading-inline">
	<?php if ( $customer->exists() ) : ?>
		<?php esc_html_e( 'Edit Customer', 'wp-ever-accounting' ); ?>
	<?php else : ?>
		<?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?>
	<?php endif; ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-customer-form" name="customer" method="post">
	<div class="eac-poststuff">
		<div class="column-1">
			<?php
			/**
			 * Fires action to inject custom meta boxes in the main column.
			 *
			 * @param Customer $customer Customer object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_customer_edit_core_meta_boxes', $customer );
			?>
		</div><!-- .column-1 -->

		<div class="column-2">

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Customer $customer Customer object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_customer_edit_side_meta_boxes', $customer );
			?>

		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_customer' ); ?>
	<input type="hidden" name="action" value="eac_edit_customer"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $customer->id ); ?>"/>
</form>
