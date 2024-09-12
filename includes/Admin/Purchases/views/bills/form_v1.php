<?php
/**
 * Admin Bills Form.
 * Page: Expenses
 * Tab: Bills
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $bill \EverAccounting\Models\Bill Bill object.
 */

defined( 'ABSPATH' ) || exit;
$columns           = eac_get_bill_columns();
$columns['action'] = '&nbsp;';
$document          = new \EverAccounting\Models\Document();
?>
	<form class="eac-document-overview" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<span data-wp-text="name"></span>
		<div class="eac-poststuff">
			<div class="column-1">
				<div class="eac-card">

					<div class="eac-document-overview__section eac-card__child document-details has-2-cols">
						<div class="col-1">
							<div class="eac-form-field">
								<label for="vendor"><?php esc_html_e( 'Vendor', 'wp-ever-accounting' ); ?></label>
								<select class="eac_select2" data-action="eac_json_search" data-type="vendor" data-placeholder="<?php esc_attr_e( 'Select a vendor', 'wp-ever-accounting' ); ?>"></select>
							</div>

							<address class="eac-document-address">
								<span class="line-company">
									Lorem Ipsum Inc.
								</span>
								<span class="lineaddress">
									1234 Main Street, Suite 200
									<br>
									New York, NY 10001
								</span>
							</address>

							</address>

						</div>

						<div class="col-2 has-2-cols">
							<?php
							eac_form_field(
								array(
									'label'       => esc_html__( 'Bill Number', 'wp-ever-accounting' ),
									'name'        => 'number',
									'value'       => $document->number,
									'type'        => 'text',
									'placeholder' => 'BILL-0001',
									'required'    => true,
								)
							);
							eac_form_field(
								array(
									'label'       => esc_html__( 'Reference', 'wp-ever-accounting' ),
									'name'        => 'reference',
									'value'       => $document->reference,
									'type'        => 'text',
									'placeholder' => 'REF-0001',
								)
							);
							eac_form_field(
								array(
									'label'       => esc_html__( 'Issue Date', 'wp-ever-accounting' ),
									'name'        => 'issue_date',
									'value'       => $document->issue_date,
									'type'        => 'text',
									'placeholder' => 'YYYY-MM-DD',
									'required'    => true,
									'class'       => 'eac_datepicker',
								)
							);

							eac_form_field(
								array(
									'label'       => esc_html__( 'Due Date', 'wp-ever-accounting' ),
									'name'        => 'due_date',
									'value'       => $document->due_date,
									'type'        => 'text',
									'placeholder' => 'YYYY-MM-DD',
									'class'       => 'eac_datepicker',
								)
							);
							?>
						</div>
					</div>

					<div class="eac-document-overview__section document-description">
						<div class="eac-form-field">
							<label for="description"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
							<textarea name="description" id="description" class="eac-textarea" rows="3"></textarea>
						</div>
					</div>

					<div class="eac-document-overview__section document-items">
						<table class="eac-document-summary">
							<thead class="eac-document-summary__head">
							<tr>
								<?php foreach ( $columns as $key => $label ) : ?>
									<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
								<?php endforeach; ?>
							</tr>
							</thead>
							<tbody class="eac-document-summary__items">
							<tr>
								<td class="col-item">
									<input type="text" class="add-line-item-name" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>">
									<textarea class="line-description" placeholder="Item Description"></textarea>
								</td>
								<td class="col-price">
									$&nbsp;<input type="number" min="1" value="1">
								</td>
								<td class="col-quantity">
									<input type="number" min="0" value="0">
								</td>
								<td class="col-tax">
									&mdash;
								</td>
								<td class="col-subtotal">
									&mdash;
								</td>
								<td class="col-action">
									<a href="#">
										<span class="dashicons dashicons-saved"></span>
									</a>
								</td>
							</tr>
							<tr>
								<td class="col-item">
									<input type="text" class="add-line-item-name" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>">
									<textarea class="line-description" placeholder="Item Description"></textarea>
								</td>
								<td class="col-price">
									$&nbsp;<input type="number" min="1" value="1">
								</td>
								<td class="col-quantity">
									<input type="number" min="0" value="0">
								</td>
								<td class="col-tax">
									&mdash;
								</td>
								<td class="col-subtotal">
									&mdash;
								</td>
								<td class="col-action">
									<a href="#">
										<span class="dashicons dashicons-saved"></span>
									</a>
								</td>
							</tr>
							<tr>
								<td class="col-item">
									<input type="text" class="add-line-item-name" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>">
									<textarea class="line-description" placeholder="Item Description"></textarea>
								</td>
								<td class="col-price">
									$&nbsp;<input type="number" min="1" value="1">
								</td>
								<td class="col-quantity">
									<input type="number" min="0" value="0">
								</td>
								<td class="col-tax">
									&mdash;
								</td>
								<td class="col-subtotal">
									&mdash;
								</td>
								<td class="col-action">
									<a href="#">
										<span class="dashicons dashicons-saved"></span>
									</a>
								</td>
							</tr>
							</tbody>
							<tbody class="eac-document-summary__actions">
							<tr>
								<td class="col-add-item left" colspan="1">
									<select class="select-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
								</td>
								<td class="col-add-item right" colspan="<?php echo count( $columns ) - 1; ?>">
									<a href="#" class="add-line-item">
										<?php esc_html_e( 'Add Tax +', 'wp-ever-accounting' ); ?>
									</a>
									&nbsp;
									<a href="#" class="add-line-item">
										<?php esc_html_e( 'Add Adjustment +', 'wp-ever-accounting' ); ?>
									</a>
								</td>
							</tbody>
							<tbody class="eac-document-summary__totals">
							<tr>
								<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
									<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
								</td>
								<td class="col-summary-amount">
									$0
								</td>
								<td class="col-action">&nbsp;</td>
							</tr>
							<tr>
								<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
									<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
								</td>
								<td class="col-summary-amount">
									$0
								</td>
								<td class="col-action">&nbsp;</td>
							</tr>
							<tr>
								<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
									<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
								</td>
								<td class="col-summary-amount">
									$0
								</td>
								<td class="col-action">&nbsp;</td>
							</tr>
							<tr>
								<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
									<?php esc_html_e( 'Adjustment', 'wp-ever-accounting' ); ?>
								</td>
								<td class="col-summary-amount">
									<input type="number" min="0" value="0">
								</td>
								<td class="col-action">&nbsp;</td>
							</tr>
							</tbody>

						</table>
					</div>

					<div class="eac-document-overview__section document-footer has-2-cols">
						<div class="eac-form-field">
							<label for="notes"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></label>
							<textarea name="notes" id="notes" class="eac-textarea" rows="5"></textarea>
						</div>
						<div class="eac-form-field">
							<label for="terms"><?php esc_html_e( 'Terms', 'wp-ever-accounting' ); ?></label>
							<textarea name="terms" id="terms" class="eac-textarea" rows="5"></textarea>
						</div>
					</div>

				</div>
			</div><!-- .column-1 -->
			<div class="column-2">
				<button type="submit" class="button button-primary button-large tw-w-full">
					<?php esc_html_e( 'Save Invoice', 'wp-ever-accounting' ); ?>
				</button>

				<hr>

				<?php
				eac_form_field(
					array(
						'label'        => esc_html__( 'Currency', 'wp-ever-accounting' ),
						'name'         => 'currency_code',
						'value'        => $document->currency_code,
						'type'         => 'select',
						'options'      => eac_get_currencies(),
						'option_value' => 'code',
						'option_label' => 'formatted_name',
						'placeholder'  => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
						'class'        => 'eac_select2',
						'data-action'  => 'eac_json_search',
						'data-type'    => 'currency',
					)
				);

				eac_form_field(
					array(
						'label'   => esc_html__( 'VAT Exempt', 'wp-ever-accounting' ),
						'name'    => 'vat_exempt',
						'value'   => $document->vat_exempt,
						'type'    => 'select',
						'options' => array(
							'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
							'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
						),
					)
				);
				?>

				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Receipt</h3>
					</div>
					<div class="eac-card__body">
						<div class="eac-attachment">
							<div class="eac-attachment__dropzone">
								<button class="eac-attachment__button" type="button">Upload Receipt</button>
							</div>
							<ul class="eac-attachment__list">
								<li class="eac-attachment__item">
									<div class="eac-attachment__icon"> <img src="https://via.placeholder.com/150" alt="Attachment"> </div>
									<div class="eac-attachment__info">
										<div class="eac-attachment__name">Receipt-0001.jpg</div>
										<div class="eac-attachment__size">1.2 MB</div>
									</div>
									<div class="eac-attachment__actions">
										<a href="#" class="eac-attachment__action">
											<span class="dashicons dashicons-download"></span>
										</a>
									</div>
								</li>
							</ul>
						</div>

					</div>
				</div>


			</div>
		</div><!-- .eac-poststuff -->
	</form>
<?php
