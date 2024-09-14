/* global Backbone, _, $ */

export const NoLineItems = wp.Backbone.View.extend( {
	tagName: 'tbody',

	className: 'eac-invoice-table__no-items',

	template: wp.template('eac-invoice-no-items'),
} );

export default NoLineItems;
