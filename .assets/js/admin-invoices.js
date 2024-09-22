(function (document, wp, $) {
	'use strict';
	var list, editor, viewer;


	editor.view.Frame = wp.Backbone.View.extend({
		className: 'media-frame wp-core-ui',
		frame: function () {
			return {
				toolbar: 'main',
				menu: 'default',
				content: this.options.content,
				footer: 'main'
			};
		},
	});

}(document, wp, jQuery));
