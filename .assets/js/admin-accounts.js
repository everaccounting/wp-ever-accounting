(function (document, window, $) {
	'use strict';

	var app = {
		events: {
			'click .wp-heading-inline': 'addItem',
		},

		addItem: function (e) {
			e.preventDefault();
			console.log('--Item added--');
			console.log(this);
			console.log('Item added');
		},

		init: function () {
			var events = this.events || {};
			for (var key in events) {
				var method = events[key];
				if (typeof method !== 'function') method = this[method];
				if (!method) continue;
				var match = key.match(/^(\S+)\s*(.*)$/);
				var eventName = match[1];
				var selector = match[2];
				$(document).on(eventName, selector, method.bind(this));
			}
		},
	};

	app.init();

}(document, window, jQuery));
