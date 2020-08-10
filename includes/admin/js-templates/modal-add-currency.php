<?php
/**
 * Add Currency Modal.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$currencies = eaccounting_get_global_currencies();
$options    = array();
foreach ( $currencies as $code => $props ) {
	$options[ $code ] = sprintf( '%s (%s)', $props['name'], $props['symbol'] );
}
$currency = new EAccounting_Currency( null );
?>
	<script type="text/template" id="tmpl-ea-modal-add-currency">
		<div class="ea-backbone-modal">
			<div class="ea-backbone-modal-content">
				<section class="ea-backbone-modal-main" role="main">
					<form id="ea-modal-currency-form" action="" method="post">
						<header class="ea-backbone-modal-header">
							<h1><?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?></h1>
							<button class="modal-close modal-close-link dashicons">
								<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
							</button>
						</header>
						<article>
							<div class="ea-row">
								<?php
								eaccounting_select( array(
										'wrapper_class' => 'ea-col-12',
										'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
										'name'          => 'code',
										'class'         => 'ea-select2',
										'value'         => $currency->get_code( 'edit' ),
										'options'       => $options,
										'required'      => true,
								) );
								eaccounting_text_input( array(
										'wrapper_class' => 'ea-col-12',
										'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
										'name'          => 'rate',
										'value'         => $currency->get_rate( 'edit' ),
										'required'      => true,
								) );
								?>
							</div>
						</article>
						<footer>
							<?php wp_nonce_field( 'edit_currency' ); ?>
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
<?php
//eaccounting_enqueue_js( "
//	jQuery(document)
//			.on('ea-modal-add-currency_loaded', function () {
//			jQuery('#ea-modal-currency-form #code').select2();
//			jQuery('#ea-modal-currency-form #rate').on('keyup', function () {
//				jQuery(this).val($(this).val().replace(/[^\d.]+/g, ''));
//			});
//		});
//" );
