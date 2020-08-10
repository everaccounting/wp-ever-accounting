<?php
/**
 * Modal Add Account Page.
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
<script type="text/template" id="tmpl-ea-modal-add-account">
	<div class="ea-backbone-modal">
		<div class="ea-backbone-modal-content">
			<section class="ea-backbone-modal-main" role="main">

				<header class="ea-backbone-modal-header">
					<h1><?php esc_html_e( 'Add Account', 'wp-ever-accounting' ); ?></h1>
					<button class="modal-close modal-close-link dashicons">
						<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
					</button>
				</header>

				<article>
					<form id="ea-modal-currency-form" action="" method="post">
						<div class="ea-row">
							<?php
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Currency Name', 'wp-ever-accounting' ),
									'name'          => 'name',
									'value'         => $currency->get_name( 'edit' ),
									'required'      => true,
							) );
							eaccounting_select( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
									'name'          => 'code',
									'class'         => 'ea-select2',
									'value'         => $currency->get_code( 'edit' ),
									'options'       => $options,
									'required'      => true,
							) );
							eaccounting_text_input( array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
									'name'          => 'rate',
									'value'         => $currency->get_rate( 'edit' ),
									'required'      => true,
							) );
							?>
						</div>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button id="btn-ok" class="button button-primary button-large"><?php esc_html_e( 'Add', 'wp-ever-accounting' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="ea-backbone-modal-backdrop modal-close"></div>
</script>
