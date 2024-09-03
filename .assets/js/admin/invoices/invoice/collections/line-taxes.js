import {LineTax} from '../models/line-tax.js';

export const LineTaxes = Backbone.Collection.extend({
	/**
	 * @since 3.0.0
	 *
	 * @type {LineTax}
	 */
	model: LineTax,
});
