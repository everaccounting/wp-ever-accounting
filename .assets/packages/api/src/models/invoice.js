import Document from './document.js';
import Customer from './customer.js';

export default Document.extend({
	endpoint: 'invoices',

	defaults: Object.assign({}, Document.prototype.defaults, {
		type: 'invoice',

		// Relationships
		customer: new Customer(),
	}),

	/**
	 * Get a new invoice number
	 *
	 * @return {Promise}
	 */
	getNewNumber() {
		var self = this;
		this.getNextNumber({
			success: (response) => {
				self.set('number', response.next_number);
			}
		})
	},

	/**
	 * Update Amount
	 *
	 * @return {void}
	 */
	updateAmounts() {
		// Update subtotals of each line item.
		var subtotal = 0;
		var total_tax = 0;
		this.get('items').updateAmounts();
		_.each(this.get('items').models, (item) => {
			subtotal += item.get('subtotal');
			total_tax += item.getTotalTax();
		});

		this.set({
			subtotal: subtotal,
			tax_total: total_tax,
		})
	},

	/**
	 * Get itemized tax total.
	 *
	 * @return {object}
	 */
	getItemizedTaxes() {
		var taxes = {};
		_.each(this.get('items').models, (item) => {
			_.each(item.get('taxes').models, (tax) => {
				// based on rate and compound flag, calculate tax amount.
				var index = `${tax.get('rate')}-${tax.get('compound')}`;
				if (taxes[index] === undefined) {
					taxes[index] = {
						amount: 0,
						rate: tax.get('rate'),
						name: tax.get('name'),
						formatted_name: tax.get('formatted_name'),
					};
				}

				taxes[index].amount += tax.get('amount');
			});
		});

		// return only values.
		return _.values(taxes);
	}

});
