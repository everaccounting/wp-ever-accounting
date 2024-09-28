<?php
/**
 * Admin View: Customer details.
 *
 * @package EverAccounting
 * @subpackage EverAccounting/Admin/Views
 * @since 1.0.0
 *
 * @var $customer \EverAccounting\Models\Customer Customer object.
 */

defined( 'ABSPATH' ) || exit;
?>

<h1 class="wp-heading-inline">
	<?php echo sprintf( esc_html__( 'Customer: #%d', 'wp-ever-accounting' ), esc_html($customer->id) ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( ['action', 'id'] ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<div class="eac-profile">
	<div class="eac-profile__avatar">
		<?php echo get_avatar( $customer->email, 120 ); ?>
	</div>

	<div class="eac-profile__content">
		<div class="eac-profile__column">
			<p><strong><?php echo esc_html( $customer->name ); ?></strong></p>
			<p><?php echo esc_html( $customer->company ); ?></p>
			<p><a href="mailto:manikdrmc@gmail.com">manikdrmc@gmail.com</a></p>
			<p><a href="tel:+01712345678">+8801712345678</a></p>
			<p><a href="https://manikdrmc.com" target="_blank">manikdrmc.com</a></p>
		</div>
		<div class="eac-profile__column">
			<h3 class="eac-profile__title"><?php esc_html_e( 'Customer Details', 'wp-ever-accounting' ); ?></h3>
		</div>
	</div>
</div>
