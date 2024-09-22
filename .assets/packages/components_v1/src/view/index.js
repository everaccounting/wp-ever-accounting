import $ from 'jquery';

export default wp.Backbone.View.extend({

	/**
	 * Blocks the UI by applying a transparent overlay to the view's element.
	 * This can be used to prevent user interaction while processing.
	 */
	blockUI() {
		this.$el.block({
			message: null,
			overlayCSS: {
				backgroundColor: 'transparent',
			},
		});
	},

	/**
	 * Unblocks the UI by removing the overlay that was applied.
	 * This allows user interaction to resume.
	 */
	unblockUI() {
		this.$el.unblock();
	},

	/**
	 * Get the current value of the associated form element.
	 *
	 * @param {string|null} name The name of the form element.
	 * @return {mixed} The value of the form element.
	 */
	data: function ( name ) {
		name = name || null;
		const data = {};
		this.$('input, select, textarea').each((_, element) => {
			const $element = $(element);
			const name = $element.attr('name');
			const type = $element.attr('type');

			if (name === 'method') return;

			if (type === 'radio') {
				data[name] = $element.is(':checked') ? $element.val() || 0 : data[name];
			} else if (type === 'checkbox') {
				data[name] = data[name] || [];
				if ($element.is(':checked')) {
					data[name].push($element.val());
				}
			} else {
				data[name] = $element.val() || '';
			}
		});
		return name ? data[name] : data;
	},
});
