import Document from './document.js';
import Customer from './customer.js';

export default Document.extend( {
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
			success: ( response ) => {
				self.set( 'number', response.next_number );
			}
		})
	},

	/**
	 * Update Amount
	 *
	 * @return {void}
	 */
	updateAmount() {
		var subtotal = 0;
		var discount_total = 0;
		var tax_total = 0;
		var total = 0;

		this.get( 'items' ).each( ( item ) => {
			subtotal += item.get( 'total' );
			discount_total += item.get( 'discount_total' );
			tax_total += item.get( 'tax_total' );
			total += item.get( 'total' );
		} );

		this.set( 'subtotal', subtotal );
		this.set( 'discount_total', discount_total );
		this.set( 'tax_total', tax_total );
		this.set( 'total', total );
	}
} );
