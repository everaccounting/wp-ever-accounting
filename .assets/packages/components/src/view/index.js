import $ from 'jquery';

export default wp.Backbone.View.extend({
	/**
	 * The base view class.
	 *
	 * @param {Object} options The options for this view.
	 */
	constructor: function (options) {
		console.log('=== View.constructor() ===');
		wp.Backbone.View.prototype.constructor.call(this, options);
		// if (this.$el.is('form')) {
		// 	console.log('=== View.constructor() - is form ===');
		// 	this.form = new Backbone.Model();
		// 	this.method = this.$el.attr('method') || 'post';
		// 	this.action = this.$el.attr('action') || '';
		// 	this._initializeForm();
		// 	this.delegateEvents({
		// 		...this.events,
		// 		'change input, textarea, select': '_updateForm',
		// 	});
		// }
	},

	preinitialize() {
		console.log('=== View.preinitialize() ===');
		wp.Backbone.View.prototype.preinitialize.apply(this, arguments);

		const element = document.querySelector(this.el);
		// console.log(this.$(this.el));
		if (element.tagName.toLowerCase() === 'form') {
			console.log('=== View.constructor() - is form ===');
			this.form = new Backbone.Model();
			this.method = element.getAttribute('method') || 'post';
			this.action = element.getAttribute('action') || '';
			this._initializeForm();
			this.delegateEvents({
				...this.events,
				'change input, textarea, select': '_updateForm',
			});
		}
	},

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
	 * Initializes the form model.
	 *
	 * @private
	 * @return {void}
	 */
	_initializeForm: function () {
		// this.$('input, textarea, select').each((_, element) => {
		// 	const $element = $(element);
		// 	const name = $element.attr('name');
		// 	const type = $element.attr('type');
		//
		// 	if (name === 'method') return;
		//
		// 	if (type === 'radio') {
		// 		this.form.set(name, $element.is(':checked') ? $element.val() || 0 : undefined);
		// 	} else if (type === 'checkbox') {
		// 		this.form.set(name, $element.is(':checked') ? [$element.val()] : []);
		// 	} else {
		// 		this.form.set(name, $element.val() || '');
		// 	}
		// });
	},

	/**
	 * Updates the form model.
	 *
	 * @private
	 * @param {Object} event The event that triggered the update.
	 */
	_updateForm: function (event) {
		const $element = $(event.target);
		const name = $element.attr('name');
		const type = $element.attr('type');

		console.log('=== _updateForm() ===');
		console.log('name: ', name);
		console.log('type: ', type);

		if (name === 'method') return;

		if (type === 'radio') {
			this.form.set(name, $element.is(':checked') ? $element.val() || 0 : undefined);
		} else if (type === 'checkbox') {
			let currentValues = this.form.get(name) || [];
			if ($element.is(':checked')) {
				currentValues.push($element.val());
			} else {
				currentValues = currentValues.filter(val => val !== $element.val());
			}
			this.form.set(name, currentValues);
		} else {
			this.form.set(name, $element.val() || '');
		}
	},
});
