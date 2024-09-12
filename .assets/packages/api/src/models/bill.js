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
		discount_amount: '',
		discount_type: '',
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

	addItem: function (item_id) {
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
				}), {merge: true});
			}
		});
	},

	updateAmounts: function () {

	},
});
