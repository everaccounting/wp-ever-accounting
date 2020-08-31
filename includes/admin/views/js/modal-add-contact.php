<?php
/**
 * Add Contact Modal.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="tmpl-ea-modal-add-contact">
	<div class="ea-backbone-modal">
		<div class="ea-backbone-modal-content">
			<section class="ea-backbone-modal-main" role="main">
				<form id="ea-modal-contact-form" class="ea-ajax-form" action="" method="post">

					<header class="ea-backbone-modal-header">
						<h1><?php esc_html_e( 'Add Contact', 'wp-ever-accounting' ); ?></h1>
						<button class="modal-close modal-close-link dashicons">
							<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
						</button>
					</header>

					<article>
						<div class="ea-row">
							<?php
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Name', 'wp-ever-accounting' ),
									'name'          => 'name',
									'value'         => '',
									'required'      => true,
							) );
							eaccounting_currency_dropdown( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Account Currency', 'wp-ever-accounting' ),
									'name'          => 'currency_code',
									'value'         => '',
									'placeholder'   => __( 'Select Currency', 'wp-ever-accounting' ),
									'ajax'          => true,
									'type'          => 'currency',
							) );
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Email', 'wp-ever-accounting' ),
									'name'          => 'email',
									'type'          => 'email',
									'value'         => '',
									'required'      => false,
							) );
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Phone', 'wp-ever-accounting' ),
									'name'          => 'phone',
									'value'         => '',
									'required'      => false,
							) );
							eaccounting_textarea( array(
									'wrapper_class' => 'ea-col-12',
									'label'         => __( 'Address', 'wp-ever-accounting' ),
									'name'          => 'address',
									'value'         => '',
									'required'      => false,
							) );
							eaccounting_hidden_input( array(
									'name'  => 'type',
									'value' => 'customer',
							) );
							eaccounting_hidden_input( array(
									'name'  => 'action',
									'value' => 'eaccounting_edit_contact',
							) );
							wp_nonce_field( 'ea_edit_contact' );
							?>
						</div>
					</article>

					<footer>
						<div class="inner">
							<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'Add', 'wp-ever-accounting' ); ?></button>
						</div>
					</footer>
				</form>
			</section>
		</div>
	</div>
	<div class="ea-backbone-modal-backdrop modal-close"></div>
</script>
