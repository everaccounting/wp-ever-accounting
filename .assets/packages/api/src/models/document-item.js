import Base from './base.js';
import DocumentItemTaxes from "../collections/document-item-taxes";

export default Base.extend( {
	endpoint: 'line-items',

	defaults: {
		id: null,
		type: 'standard',
		name: '',
		price: 0,
		quantity: 1,
		subtotal: 0,
		subtotal_tax: 0,
		discount: 0,
		discount_tax: 0,
		tax_total: 0,
		total: 0,
		taxable: false,
		description: '',
		unit: '',
		item_id: null,
		updated_at: '',
		created_at: '',

		// Relationships
		taxes: new DocumentItemTaxes(),
	},

	/**
	 * Update Amount
	 *
	 * @return {void}
	 */
	updateAmount() {
		var subtotal = this.get( 'price' ) * this.get( 'quantity' );
		this.set( 'subtotal', subtotal );
	}

} );
