<?php
/**
 * Add Category Modal.
 *
 * @package     Ever_Accounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 */

use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Misc;

defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="tmpl-ea-modal-add-category">
	<div class="ea-modal">
		<div class="ea-modal-content">
			<section class="ea-modal-main" role="main">
				<form id="ea-modal-account-form" action="" method="post">

					<header class="ea-modal-header">
						<h1><?php esc_html_e( 'Add Category', 'wp-ever-accounting' ); ?></h1>
						<button class="modal-close modal-close-link dashicons">
							<span class="screen-reader-text"><?php _e( 'Close', 'wp-ever-accounting' ); ?>></span>
						</button>
					</header>

					<article>
						<div class="ea-row">
							<?php
							Form::text_input(
								array(
									'wrapper_class' => 'ea-col-12',
									'label'         => __( 'Category Name', 'wp-ever-accounting' ),
									'name'          => 'name',
									'value'         => '',
									'required'      => true,
								)
							);
							Form::text_input(
								array(
									'wrapper_class' => 'ea-col-12',
									'label'         => __( 'Color', 'wp-ever-accounting' ),
									'name'          => 'color',
									'data_type'     => 'color',
									'value'         => Misc::get_random_color(),
									'required'      => true,
								)
							);
							Form::hidden_input(
								array(
									'name'  => 'type',
									'value' => 'income',
								)
							);
							Form::hidden_input(
								array(
									'name'  => 'action',
									'value' => 'ever_accounting_edit_category',
								)
							);
							wp_nonce_field( 'ea_edit_category' );
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
