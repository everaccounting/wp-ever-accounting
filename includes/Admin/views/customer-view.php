<?php
/**
 * Admin View: Customer details.
 *
 * @since 1.0.0
 *
 * @subpackage EverAccounting/Admin/Views
 * @package EverAccounting
 * @var $customer Customer Customer object.
 */

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

wp_verify_nonce( '_wpnonce' );
$id       = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
$section  = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
$customer = EAC()->customers->get( $id );
$sections = apply_filters(
	'eac_customer_view_sections',
	array(
		'overview' => __( 'Overview', 'wp-ever-accounting' ),
		'payments' => __( 'Payments', 'wp-ever-accounting' ),
		'invoices' => __( 'Invoices', 'wp-ever-accounting' ),
		'notes'    => __( 'Notes', 'wp-ever-accounting' ),
	)
);

// Validate section.
$section = ! array_key_exists( $section, $sections ) ? current( array_keys( $sections ) ) : $section;
?>

<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'View Customer', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>
	<a href="<?php echo esc_url( $customer->get_edit_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Edit Customer', 'wp-ever-accounting' ); ?></a>
</div>

<div class="eac-card eac-profile-header">
	<div class="eac-profile-header__avatar">
		<?php echo get_avatar( $customer->email, 120 ); ?>
	</div>
	<div class="eac-profile-header__columns">
		<div class="eac-profile-header__column">
			<div class="eac-profile-header__title">
				<?php echo esc_html( $customer->name ); ?>
			</div>
			<?php if ( $customer->phone ) : ?>
				<p class="small"><a href="tel:<?php echo esc_attr( $customer->phone ); ?>"><?php echo esc_html( $customer->phone ); ?></a></p>
			<?php endif; ?>
			<?php if ( $customer->email ) : ?>
				<p class="small"><a href="mailto:<?php echo esc_attr( $customer->email ); ?>"><?php echo esc_html( $customer->email ); ?></a></p>
			<?php endif; ?>
			<p class="small">
				<?php // translators: %s: date. ?>
				<?php printf( esc_html__( 'Since %s', 'wp-ever-accounting' ), esc_html( wp_date( get_option( 'date_format' ), strtotime( $customer->created_at ) ) ) ); ?>
			</p>
		</div>
	</div>
	<div class="eac-profile-header__id">
		#<?php echo esc_html( $customer->id ); ?>
	</div>
</div>


<div class="eac-poststuff is--alt">
	<div class="column-1">
		<div class="eac-card">
			<ul class="eac-profile-nav" role="tablist">
				<?php foreach ( $sections as $section_id => $label ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'section', $section_id ) ); ?>" class="<?php echo $section === $section_id ? 'active' : ''; ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="eac-card__body">

				<?php
				/**
				 * Fires action to display customer view section.
				 *
				 * @param Customer $customer Customer object.
				 *
				 * @since 2.0.0
				 */
				do_action( 'eac_customer_view_section_' . $section, $customer );
				?>

			</div>
		</div>

		<?php
		/**
		 * Fires action to inject custom meta boxes in the main column.
		 *
		 * @param Customer $customer Customer object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_customer_view_core_meta_boxes', $customer );
		?>

	</div><!-- .column-1 -->

	<div class="column-2">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Customer Details', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-list has--split has--hover">
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->name ? esc_html( $customer->name ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Company', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->company ? esc_html( $customer->company ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->email ? esc_html( $customer->email ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Phone', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->phone ? esc_html( $customer->phone ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Website', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->website ? esc_html( $customer->website ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Tax Number', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->tax_number ? esc_html( $customer->tax_number ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->address ? esc_html( $customer->address ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'City', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->city ? esc_html( $customer->city ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'State', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->state ? esc_html( $customer->state ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Zip', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->zip ? esc_html( $customer->zip ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Country', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->country ? esc_html( $customer->formatted_country ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
				<div class="eac-list__item">
					<div class="eac-list__label"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></div>
					<div class="eac-list__value"><?php echo $customer->currency ? esc_html( $customer->currency ) : esc_html__( 'N/A', 'wp-ever-accounting' ); ?></div>
				</div>
			</div>
		</div>
		<?php
		/**
		 * Fires action to inject custom meta boxes in the side column.
		 *
		 * @param Customer $customer Customer object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_customer_view_side_meta_boxes', $customer );
		?>

	</div><!-- .column-2 -->
</div>
