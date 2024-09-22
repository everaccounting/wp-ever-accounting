import Base from './base';
import DocumentTax from '../models/document-tax';

export default Base.extend({
	model: DocumentTax,

	/**
	 * Update Amounts
	 *
	 * @param {Number} amount to apply.
	 */
	updateAmounts(amount) {
		// Reset to 0.
		this.each(tax => tax.set('amount', 0));
		// Calculate simple taxes.
		this.where({compound: false}).forEach(tax => {
			tax.set('amount', amount * parseFloat(tax.get('rate')) / 100);
		});
		// Calculate compound taxes.
		const previousTotal = this.reduce((acc, tax) => acc + tax.get('amount'), 0);
		this.where({compound: true}).forEach(tax => {
			tax.set('amount', (amount + previousTotal) * parseFloat(tax.get('rate')) / 100);
		});
	},

	/**
	 * Get Total Tax
	 *
	 * @return {Number}
	 */
	getTotal() {
		return this.reduce((acc, tax) => acc + tax.get('amount'), 0);
	}
});
