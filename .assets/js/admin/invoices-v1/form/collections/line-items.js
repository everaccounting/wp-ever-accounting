/* global Backbone, _, $ */

import {LineItem} from "../models/line-item";

export const LineItems = Backbone.Collection.extend({
	model: LineItem,
	preinitialize: function (models, options) {
		this.options = options;
	},
});

export default LineItems;
