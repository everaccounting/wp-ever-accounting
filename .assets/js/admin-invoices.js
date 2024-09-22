(function (document, wp, $) {
	'use strict';
	var invoice = {};

	/**
	 * Invoice billing address view.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.BillingAddress = wp.Backbone.View.extend({});

	/**
	 * Invoice Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.Items = wp.Backbone.View.extend({});

	/**
	 * Invoice Items No Items View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.NoItems = wp.Backbone.View.extend({});

	/**
	 * Invoice Items Item View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.Item = wp.Backbone.View.extend({});

	/**
	 * Invoice toolbar view.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.Toolbar = wp.Backbone.View.extend({});

	/**
	 * Invoice Totals View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.Totals = wp.Backbone.View.extend({});

	/**
	 * Invoice Editor Form View.
	 *
	 * @type {Backbone.View}
	 * @since 1.0.0
	 */
	invoice.view.Form = wp.Backbone.View.extend({});

}(document, wp, jQuery));
