<?php
/**
 * Add Account Modal.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="tmpl-ea-modal-add-account">
	<div class="ea-backbone-modal">
		<div class="ea-backbone-modal-content">
			<section class="ea-backbone-modal-main" role="main">
				<form id="ea-modal-account-form" action="" method="post">

					<header class="ea-backbone-modal-header">
						<h1><?php esc_html_e( 'Add Account', 'wp-ever-accounting' ); ?></h1>
						<button class="modal-close modal-close-link dashicons">
							<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
						</button>
					</header>

					<article>
						<div class="ea-row">
							<?php
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Account Name', 'wp-ever-accounting' ),
									'name'          => 'name',
									'value'         => '',
									'required'      => true,
							) );
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Account Number', 'wp-ever-accounting' ),
									'name'          => 'number',
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
									'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
									'name'          => 'opening_balance',
									'value'         => '',
									'default'       => '0.00',
									'required'      => true,
							) );
							eaccounting_hidden_input( array(
									'name'  => 'action',
									'value' => 'eaccounting_edit_account',
							) );

							wp_nonce_field( 'ea_edit_account' );
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
