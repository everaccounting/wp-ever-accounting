/* global Backbone, _, $ */

import AddLineItem from "./add-line-item";

export const Actions = wp.Backbone.View.extend( {
	tagName: 'tfoot',

	className: 'eac-invoice-table__actions',

	template: wp.template('eac-invoice-actions'),

	events: {
		'click .add-line-item': 'onAddLineItem',
		'click .add-tax': 'onAddTax',
	},

	onAddLineItem( e ) {
		e.preventDefault();
		new AddLineItem( this.options ).open().render();
		// new AddLineItem( this.options ).render().open();
		jQuery(document.body).trigger('eac_update_ui');
	},

	onAddTax( e ) {
		e.preventDefault();

		console.log('onAddTax');
	},
} );

export default Actions;
