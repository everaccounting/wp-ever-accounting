import Base from './base.js';
import DocumentItems from '../collections/document-items';

export default Base.extend({
	defaults: {
		id: '',
		type: '',
		status: 'draft',
		number: '',
		contact_id: '',
		subtotal: 0,
		discount: 0,
		tax_total: 0,
		total: 0,
		discount_value: 0,
		discount_type: 'fixed',

		reference: '',
		note: '',
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

		// Relationships
		address: {},
		currency: {},
		items: new DocumentItems(),

		// Flags
		is_fetching: false,
	},

	getNextNumber(options = {}) {
		return this.fetch({
			url: `${this.apiRoot}${this.namespace}/utilities/next-number?type=${this.get('type')}`,
			type: 'GET',
			...options,
		})
	},


	/**
	 * Update Amount
	 *
	 * @return {void}
	 */
	updateAmounts() {
		console.log('=== Bill.updateAmounts() ===');
		var items_total = 0,
			subtotal = 0,
			discount = 0,
			tax_total = 0,
			total = 0,
			discount_type = this.get('discount_type') || 'fixed',
			discount_value = parseFloat(this.get('discount_value'), 10) || 0;

		console.log('discount_type:', discount_type);
		console.log('discount_value:', discount_value);

		// Prepare items for calculation
		_.each(this.get('items').models, (item) => {
			const _price = parseFloat(item.get('price'), 10) || 0;
			const _quantity = parseFloat(item.get('quantity'), 10) || 0;
			const _subtotal = _price * _quantity;
			const _type = item.get('type') || 'standard';
			// if standard, add to items_total.
			if (_type === 'standard') {
				items_total += _subtotal;
			}

			item.set({
				price: _price,
				quantity: _quantity,
				subtotal: _subtotal,
				type: _type,
				discount: 0,
			});
		});

		discount = discount_type === 'percent' ? (items_total * discount_value) / 100 : discount_value;
		discount = discount > items_total ? items_total : discount;


		_.each(this.get('items').models, (item) => {
			var _type = item.get('type') || 'standard',
				_subtotal = item.get('subtotal') || 0,
				_discount = _type === 'standard' ? (discount / items_total) * _subtotal : 0,
				_disc_subtotal = Math.max(_subtotal - _discount, 0);

			console.log('type:', _type);
			console.log('discount:', _discount);

			// Simple tax calculation.
			_.each(item.get('taxes').models, (tax) => {
				const _tax_rate = parseFloat(tax.get('rate'), 10) || 0;
				const _tax_amount = !tax.get('compound') ? _disc_subtotal * (_tax_rate / 100) : 0;
				tax.set({
					rate: _tax_rate,
					amount: _tax_amount,
				});
			});

			const _prev_tax = _.reduce(item.get('taxes').models, (sum, tax) => {
				return sum + tax.get('amount')
			}, 0);

			_.each(item.get('taxes').where({compound: true}), (tax) => {
				const _tax_rate = parseFloat(tax.get('rate'), 10) || 0;
				const _tax_amount = (_disc_subtotal + _prev_tax) * (_tax_rate / 100);
				tax.set({
					rate: _tax_rate,
					amount: _tax_amount,
				});
			});

			const _tax_total = _.reduce(item.get('taxes').models, (sum, tax) => {
				return sum + tax.get('amount')
			}, 0);

			item.set({
				discount: _discount,
				subtotal: _disc_subtotal,
				tax_total: _tax_total,
				total: _disc_subtotal + _tax_total,
			});

			subtotal += _disc_subtotal;
			tax_total += _tax_total;
		});


		total = subtotal + tax_total;
		this.set({
			subtotal: subtotal,
			discount: discount,
			tax_total: tax_total,
			total: total,
		});
	},
});
