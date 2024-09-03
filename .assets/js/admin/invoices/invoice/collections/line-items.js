import {LineItem} from '../models/line-item.js';

export const LineItems = Backbone.Collection.extend({
	/**
	 * @since 3.0.0
	 *
	 * @type {LineItem}
	 */
	model: LineItem,
});
