/* global Backbone, _, $ */

export const Totals = wp.Backbone.View.extend( {
	tagName: 'tbody',

	className: 'eac-invoice-table__totals',

	template: wp.template('eac-invoice-totals'),
} );

export default Totals;
