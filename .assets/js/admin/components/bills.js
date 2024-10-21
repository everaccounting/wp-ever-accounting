import apiFetch from '@wordpress/api-fetch';

jQuery(document).ready(($) => {
	'use strict';
	$('#eac-edit-bill').eac_form({
		events: {
			'change :input[name="contact_id"]': 'onChangeContact',
			'change :input[name="currency"]': 'onChangeCurrency',
			'select2:select .add-item': 'onAddItem',
			'click .remove-item': 'onRemoveItem',
			'change .item-price, .item-quantity': 'onChangeItem',
			'select2:select .item-taxes': 'onAddTax',
			'select2:unselect .item-taxes': 'onRemoveTax',
			'change :input[name="discount_type"], :input[name="discount_value"]': 'onChangeDiscount',
		},

		onChangeContact(e) {
			const self = this;
			const data = self.getValues();
			data.action = 'eac_get_bill_address';
			self.block();
			$.post(ajaxurl, data, function (r) {
				self.unblock();
				const res = wpAjax.parseAjaxResponse(r, 'data');
				if (!res || res.errors) {
					self.$('.document-address').html('');
					return;
				}
				self.$('.document-address').html(res.responses[0].data);
			});
		},

		onChangeCurrency(e) {
			var currency = $(e.target).val();
			var config = eac_currencies[currency] || eac_currencies[eac_base_currency];
			var $exchange = $(':input[name="exchange_rate"]');
			$exchange.val(config?.rate || 1).removeClass('enhanced').data('currency', currency).attr('readonly', currency === eac_base_currency);
			$(document.body).trigger('eac_update_ui');
			this.updateTotals();
		},

		onAddItem(e) {
			const self = this;
			const params = e.params.data;
			const nextIndex = _.uniqueId();
			self.block();
			$( e.target ).val( null ).trigger( 'change' );
			const exchange_rate = (self.$(':input[name="exchange_rate"]').inputmask('unmaskedvalue') || 1);
			apiFetch({path: 'eac/v1/items/' + params.id}).then(function (item) {
				const data = {}
				data['items[' + nextIndex + '][item_id]'] = item.id;
				data['items[' + nextIndex + '][name]'] = item.name;
				data['items[' + nextIndex + '][description]'] = item.description;
				data['items[' + nextIndex + '][price]'] = (item.cost || item.price) * exchange_rate;
				data['items[' + nextIndex + '][quantity]'] = 1;
				data['items[' + nextIndex + '][type]'] = item.type;
				data['items[' + nextIndex + '][unit]'] = item.unit;

				if (item.taxes) {
					item.taxes.forEach(function (tax) {
						const taxIndex = _.uniqueId();
						data['items[' + nextIndex + '][taxes][' + taxIndex + '][tax_id]'] = tax.id;
						data['items[' + nextIndex + '][taxes][' + taxIndex + '][name]'] = tax.name;
						data['items[' + nextIndex + '][taxes][' + taxIndex + '][rate]'] = tax.rate;
						data['items[' + nextIndex + '][taxes][' + taxIndex + '][compound]'] = tax.compound || false;
					});

					self.updateTotals(data);
				}
			});
		},

		onRemoveItem(e) {
			const self = this;
			const $row = $(e.target).closest('tr');
			$row.remove();
			self.updateTotals();
		},

		onChangeItem(e) {
			this.updateTotals();
		},

		onAddTax(e) {
			const self = this;
			const params = e.params.data;
			const $row = $(e.target).closest('tr');
			const rowIndex = $row.data('index');
			const nextIndex = _.uniqueId();
			const data = {
				...self.getValues(),
				['items[' + rowIndex + '][taxes][' + nextIndex + '][tax_id]']: params.id,
				['items[' + rowIndex + '][taxes][' + nextIndex + '][name]']: params.name,
				['items[' + rowIndex + '][taxes][' + nextIndex + '][rate]']: params.rate,
				['items[' + rowIndex + '][taxes][' + nextIndex + '][compound]']: params.compound || false,
			}
			self.updateTotals(data);
		},

		onRemoveTax(e) {
			const self = this;
			const values = self.getValues();
			const params = e.params.data;
			const $row = $(e.target).closest('tr');
			const rowId = $row.data('index');
			for (const key in values) {
				// we will find the items[rowId][taxes][0][tax_id] = params.id then remove all the keys of that taxes.
				const match = key.match(/^items\[(\d+)\]\[taxes\]\[(\d+)\]\[tax_id\]$/);
				if (!match || match[1] !== rowId.toString() || values[key] !== params.id.toString()) {
					continue;
				}
				// now have to remove all the input fields having name items[rowId][taxes][match[2]]...
				$row.find(`input[name^="items[${rowId}][taxes][${match[2]}]"]`).remove()
			}

			setTimeout(function() {
				self.$(e.target).select2('close');
				self.updateTotals();
			}, 0);
		},

		onChangeDiscount(e) {
			this.updateTotals();
		},

		updateTotals(data) {
			var self = this;
			data = {
				...this.getValues(),
				...data || {},
				action: 'eac_get_recalculated_bill',
			}
			self.block();
			const activeElement = document.activeElement;
			$.post(ajaxurl, data, function (r) {
				const res = wpAjax.parseAjaxResponse(r);
				if (!res || res.errors) {
					return;
				}
				self.$('.eac-document-items__items').html(res.responses[0].data);
				self.$('.eac-document-items__totals').html(res.responses[1].data);
				self.unblock();
				$(document.body).trigger('eac_update_ui');
				activeElement.focus();
			});
		}
	});
});
