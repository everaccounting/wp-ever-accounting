import Base from './base.js';

export default Base.extend({
	endpoint: 'bills',

	defaults: {
		id: '',
		type: '',
		status: '',
		number: '',
		contact_id: '',
		subtotal: '',
		discount_total: '',
		tax_total: '',
		total: '',
		total_paid: '',
		discount_amount: 0,
		discount_type: 'fixed',
		billing_data: '',
		reference: '',
		note: '',
		vat_exempt: 'no',
		issue_date: '',
		due_date: '',
		sent_date: '',
		payment_date: '',
		currency_code: '',
		exchange_rate: '',
		parent_id: '',
		created_via: '',
		creator_id: '',
		uuid: '',
		updated_at: '',
		created_at: '',

		billing_name: '',
		billing_address: '',
		billing_city: '',
		billing_state: '',
		billing_zip: '',
		billing_country: '',
		billing_phone: '',
		billing_email: '',
		billing_vat: '',

		is_fetching: false,
	},

	addItem: function (item_id, silent = false ) {
		var self = this;
		self.set('is_fetching', true);
		new eac.api.models.Item({
			id: item_id,
		}).fetch({
			success: function (item) {
				// if vat_exempt or item is not taxable then add the item without taxes.
				if (!_.isEmpty(item.get('tax_ids')) && ('yes' !== self.get('vat_exempt') || item.get('taxable'))) {
					item.getTaxes().success(function (taxes) {

						var lineTaxes = new eac.api.collections.LineTaxes(null, {state: self});
						_.each(taxes, function (tax) {
							lineTaxes.add(new eac.api.models.LineTax({
								...tax,
								tax_id: tax.id,
								id: _.uniqueId('tax_'),
							}));
						});

						item.set('taxes', lineTaxes);
					});
				}
				self.set('is_fetching', false);
				self.get('items').add(new eac.api.models.LineItem({
					...item.toJSON(),
					id: _.uniqueId('item_'),
					item_id: item_id,
					quantity: 1,
					subtotal: item.get('price') * 1,
					description: item.get('description').length > 160 ? item.get('description').substring(0, 160) : item.get('description')
				}), {merge: true });

				self.updateAmounts();
			}
		});
	},

	updateAmounts: function () {
		var self = this;
		var subtotal = 0;
		var tax_total = 0;
		var discount_total = 0;
		var total = 0;
		var total_paid = 0;

		const discount_amount = parseFloat(self.get('discount_amount')) || 0;
		const discount_type = self.get('discount_type') || 'fixed';
		console.log('=== UpdateAmounts() ===');
		console.log('discount_amount: ', discount_amount);
		console.log('discount_type: ', discount_type);

		self.get('items').each(function (item) {
			console.log('=== item ===');
			const _subtotal = parseFloat(item.get('quantity')) * parseFloat(item.get('price'));
			console.log('subtotal: ',  _subtotal);
			const _subtotal_tax = self.calculateTax(_subtotal, item.get('taxes'));
			console.log('subtotal_tax: ',  _subtotal_tax);
			const _discount = discount_type === 'percentage' ? _subtotal * discount_amount / 100 : discount_amount;
			console.log('discount: ',  _discount);
			const _discount_tax = self.calculateTax(_discount, item.get('taxes'));
			console.log('discount_tax: ',  _discount_tax);
			const _tax_total = item.get('subtotal_tax') - item.get('discount_tax');
			console.log('tax_total: ',  _tax_total);
			const _total = _subtotal + _subtotal_tax - _discount - _discount_tax;
			console.log(_total);
			item.set({
				subtotal: _subtotal,
				discount: _discount,
				tax_total: _tax_total,
				total: _total
			});

			subtotal += _subtotal;
			discount_total += _discount;
			tax_total += _tax_total;
			total += _total;
		});

		self.set({
			subtotal: subtotal,
			tax_total: tax_total,
			total: total,
			total_paid: total_paid,
		});

		// self.trigger('change:amounts');
	},

	calculateTax(price, taxes) {
		let simple_tax = 0;
		let compound_tax = 0;
		// If taxes is a collection then convert it toJSON.
		taxes = taxes.toJSON ? taxes.toJSON() : taxes;

		// Simple tax calculation.
		_.each(taxes.filter((tax) => !tax.is_compound), (tax) => {
			simple_tax += price * parseFloat(tax.rate) / 100;
		});

		// Compound tax calculation.
		_.each(taxes.filter((tax) => tax.is_compound), (tax) => {
			compound_tax += (price + simple_tax) * parseFloat(tax.rate) / 100;
		});

		return simple_tax + compound_tax;
	}
});
