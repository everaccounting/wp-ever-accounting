import Document from './document.js';
import Customer from './customer.js';

export default Document.extend({
	endpoint: 'bills',

	defaults: Object.assign({}, Document.prototype.defaults, {
		type: 'bill',

		// Relationships
		// customer: new Customer(),
	}),
});
