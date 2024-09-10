/* global Backbone, _, $ */

export const LineTax = Backbone.Model.extend({
	defaults: {
		id: null,
		name: '',
		rate: 0,
		amount: 0,
		total: 0,
	},
});


export default LineTax;
