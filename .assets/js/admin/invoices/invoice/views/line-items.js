export const LineItems = wp.Backbone.View.extend({
	/**
	 * The tag name for the view's element.
	 *
	 * @type {string}
	 */
	tagName: 'tbody',

	/**
	 * The class name for the view's element.
	 *
	 * @type {string}
	 */
	className: 'invoice-line-items',

	/**
	 * Initialize the view.
	 */
	initialize() {
		console.log('LineItems view initialized');
	},

	/**
	 * Render the view.
	 *
	 * @returns void
	 */
	render() {
		console.log('LineItems view rendered');
	}
});
