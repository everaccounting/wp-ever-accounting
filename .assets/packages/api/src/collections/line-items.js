import Base from './base';
import LineItem from '../models/line-item';

export default Base.extend({
	endpoint: 'line-items',

	model: LineItem,

	updateAmounts() {
		const {options} = this;
		const {state} = options;

		const items = state.get('items');
		const discount_amount = parseFloat(state.get('discount_amount'));
		const discount_type = state.get('discount_type');
		let subtotal = 0,
			discount_total = 0,
			tax_total = 0,
			total = 0;


		_.each(items.models, (model) => {
			const _subtotal = parseFloat(model.get('quantity')) * parseFloat(model.get('price'));
			const _subtotal_tax = this.calculateTax(_subtotal, model.get('taxes'));
			const _discount = discount_type === 'percentage' ? _subtotal * discount_amount / 100 : discount_amount;
			const _discount_tax = this.calculateTax(_discount, model.get('taxes'));
			const _tax_total = model.get('subtotal_tax') - model.get('discount_tax');
			const _total = _subtotal + _subtotal_tax - _discount - _discount_tax;

			// silently set the values.
			model.set({
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

		state.set('subtotal', Math.max(0, subtotal));
		state.set('discount_total', Math.max(0, discount_total));
		state.set('tax_total', Math.max(0, tax_total));
		state.set('total', Math.max(0, total));
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
