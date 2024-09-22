import Modal from './modal';

(function ($) {
	'use strict';
	/**
	 * Initialize the modal plugin.
	 * @param {Object} options - Configuration options for the modal.
	 * @returns {jQuery} - The jQuery object for chaining.
	 */
	$.fn.eacmodal = function (options) {
		options = options || {};
		if (typeof options === 'object' || !options) {
			return this.each(function () {
				const instance = new $.eacmodal($(this), options);
				$(this).data('eacmodal', instance); // Store the instance in data
			});
		}

		var ret = this;
		var args = Array.prototype.slice.call(arguments, 1);
		this.each(function () {
			var instance = $(this).data('eacmodal');
			if (instance && typeof instance?.view[options] === 'function') {
				ret = instance.view[options].apply(instance.view, args);
			}
		});
		return ret;
	};

	/**
	 * Modal constructor.
	 * @param {jQuery} $el - The jQuery element representing the modal.
	 * @param {Object} options - Configuration options for the modal.
	 * @returns {$.eacmodal} - The modal instance.
	 */
	$.eacmodal = function ($el, options) {
		this.$el = $el;
		this.options = $.extend({}, $.eacmodal.defaults, options || {});
		if (this.options.template) {
			this.view = new $.eacmodal.View(this.options).render();
			this.$el.data('eacmodal', this);
		}
		return this;
	};

	$.eacmodal.defaults = {
		template: '',
		model: {},
		autoOpen: true,
		onOpen: function () {
		},
		onClose: function () {
		}
	};

	$.eacmodal.View = Modal;

})(jQuery, Backbone, _);
