/* global Backbone, _, jQuery, eac_invoices_vars */


/**
 * ========================================================================
 * INVOICE FORM HANDLER
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';
	const FORM_ID = '#eac-invoice-form';

	/**
	 * ========================================================================
	 * VIEWS
	 * ========================================================================
	 */

	var NoLineItemsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'no-items',

		template: wp.template('eac-invoice-no-line-items'),
	});

	var LineItemsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__items',

		template: wp.template('eac-invoice-line-items'),
	});

	var ActionsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__actions',

		template: wp.template('eac-invoice-actions'),

		events: {
			'change .add-line-item': 'onAddLineItem',
		},

		onAddLineItem(e) {
			e.preventDefault();
			var self = this;
			var $target = $(e.target);
			var item_id = parseInt($target.val(), 10);
			if (!item_id) {
				return;
			}
			$target.val('').trigger('change');
			self.startSpinner();
			console.log('onAddLineItem');
		},

		startSpinner() {
			this.$el.find('.spinner').addClass('is-active');
		},

		stopSpinner() {
			this.$el.find('.spinner').removeClass('is-active');
		}
	});

	var TotalsView = wp.Backbone.View.extend({
		tagName: 'tbody',

		className: 'eac-document-summary__totals',

		template: wp.template('eac-invoice-totals'),
	});

	var FormView = wp.Backbone.View.extend({
		el: FORM_ID,

		render() {
			this.views.add('.eac-document-summary', new NoLineItemsView(this.options));
			this.views.add('.eac-document-summary', new ActionsView(this.options));
			this.views.add('.eac-document-summary', new TotalsView(this.options));
			$(document.body).trigger('eac_update_ui');
			return this;
		}
	});

	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		if (!$(FORM_ID).length) {
			return;
		}

		var formView = new FormView();
		formView.render();
	};

	$(int);
});
