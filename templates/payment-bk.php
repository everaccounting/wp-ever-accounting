<?php
defined( 'ABSPATH' ) || exit();
eac_get_header();
$company_name = get_option( 'eac_company_name', get_bloginfo( 'name' ) );
$logo         = get_option( 'eac_company_logo', '' );
?>

<div class="eac-container">
	<div class="eac-columns">
		<div class="eac-col-9 col-md-offset-1">
			<div class="eac-panel is-large eac-document mt-0">
				<div class="eac-document__header">
					<div class="eac-document__logo">
						<a href="https://pluginever.com">
							<?php if ( $logo ) : ?>
								<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
							<?php else : ?>
								<h2><?php echo esc_html( $company_name ); ?></h2>
							<?php endif; ?>
						</a>
					</div>
					<div class="eac-document__title">
						<div class="eac-document__title-text">Payment RECEIPT</div>
						<div class="eac-document__title-meta">#Inv-0001</div>
					</div>
				</div>

				<div class="eac-document__body">
					<div class="eac-document__section document-details">
						<div class="eac-document__from">
							<h4 class="eac-document__section-title">Paid to</h4>
							<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
						</div>
						<div class="eac-document__to">
							<h4 class="eac-document__section-title">Paid By</h4>
							<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
						</div>
					</div>
					<div class="eac-document__section document-items">
						<div class="eac-document__subject"></div>
						<table class="eac-document__items">
							<tbody>
								<tr>
									<th scope="col">
										Payment Date
									</th>
									<td>
										<?php echo esc_html( date( 'd M, Y' ) ); ?>
									</td>
								</tr>
								<tr>
									<th scope="col">
										Payment Method
									</th>
									<td>
										<?php echo esc_html( 'Cash' ); ?>
									</td>
								</tr>
								<tr>
									<th scope="col">
										Reference
									</th>
									<td>
										<?php echo esc_html( 'Cash' ); ?>
									</td>
								</tr>
								<tr>
									<th scope="col">
										Amount
									</th>
									<td>
										<?php echo esc_html( 'Cash' ); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="eac-document__section document-totals">
						<div class="eac-document__notes">
							<h4>Notes:</h4>
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, adipisci aliquam
								asperiores atque autem consequatur cumque cupiditate.</p>
						</div>
					</div>
				</div>
				<div class="eac-document__footer">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus, minima.
				</div>
			</div>
		</div>
	</div>
</div>

<?php

eac_get_footer();
