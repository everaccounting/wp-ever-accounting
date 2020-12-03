<?php
/**
 * Add Tax Modal.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Js Templates
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="tmpl-ea-modal-add-tax">
	<div class="ea-modal">
		<div class="ea-modal-content">
			<section class="ea-modal-main" role="main">
				<form id="ea-modal-tax-form" action="" method="post">

					<header class="ea-modal-header">
						<h1><?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?></h1>
						<button class="modal-close modal-close-link dashicons">
							<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
						</button>
					</header>

					<article>
						<div class="ea-row">
							<?php
							eaccounting_text_input(
								array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Tax Name', 'wp-ever-accounting' ),
									'name'          => 'name',
									'value'         => '',
									'required'      => true,
								)
							);
							eaccounting_text_input(
								array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Tax Rate', 'wp-ever-accounting' ),
									'name'          => 'rate',
									'required'      => true,
								)
							);
							eaccounting_hidden_input(
								array(
									'name'  => 'type',
									'value' => 'fixed',
								)
							);
							eaccounting_hidden_input(
								array(
									'name'  => 'action',
									'value' => 'eaccounting_edit_tax',
								)
							);
							wp_nonce_field( 'ea_edit_tax' );
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
	<div class="ea-modal-backdrop modal-close"></div>
</script>
