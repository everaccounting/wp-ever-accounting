(function (document, wp, $) {
	'use strict';

	const currencies = {};

	currencies.Table = wp.Backbone.View.extend({
		tagName: 'table',
		className: 'eac-currencies-table',
		template: wp.template('eac-currencies-table'),
	});

	currencies.Rows = wp.Backbone.View.extend({});

	currencies.Row = wp.Backbone.View.extend({});

	currencies.Empty = wp.Backbone.View.extend({});

	currencies.Actions = wp.Backbone.View.extend({});

	currencies.Model = Backbone.Model.extend({
		defaults: {
			code: '',
			name: '',
			symbol: '',
			precision: 2,
			position: 'before',
		},
	});

	currencies.Collection = Backbone.Collection.extend({
		model: currencies.Model,
	});



}(document, wp, jQuery));
