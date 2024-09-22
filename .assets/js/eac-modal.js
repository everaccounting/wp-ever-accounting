(function ($) {
	'use strict';
	const CLOSE_TRIGGER = 'data-eacmodal-close';
	const FOCUSABLE_ELEMENTS = [
		'a[href]',
		'area[href]',
		'input:not([disabled]):not([type="hidden"]):not([aria-hidden])',
		'.select2-hidden-accessible',
		'select:not([disabled]):not([aria-hidden])',
		'textarea:not([disabled]):not([aria-hidden])',
		'button:not([disabled]):not([aria-hidden])',
		'iframe',
		'object',
		'embed',
		'[contenteditable]',
	];

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

	/**
	 * Modal view constructor.
	 *
	 * @param {Object} options - Configuration options for the modal view.
	 * @returns {$.eacmodal.View} - The modal view instance.
	 */
	$.eacmodal.View  = wp.Backbone.View.extend({

		tagName: 'div',

		className: 'eac-modal',

		attributes: {
			'aria-hidden': 'true',
			'role': 'dialog',
		},

		/**
		 * Pre-initialization settings for the view.
		 * @param {Object} options - Configuration options for the modal view.
		 */
		preinitialize: function (options) {
			this.options = options;
			const {template} = this.options;
			if (template && typeof template === 'string') {
				this.template = wp.template(template);
			}
			this.activeElement = null;
			wp.Backbone.View.prototype.preinitialize.apply(this, arguments);
		},

		/**
		 * Returns the options for this view.
		 * @return {Object} The options for this view.
		 */
		prepare() {
			const input = this.model || {};
			return _.pick(input,
				_.filter(_.keys(input), key =>
					_.isString(input[key]) ||
					_.isNumber(input[key]) ||
					_.isBoolean(input[key])
				)
			);
		},

		/**
		 * Renders the modal content.
		 * @returns {$.eacmodal.View} - The modal view instance.
		 */
		render: function () {
			wp.Backbone.View.prototype.render.apply(this, arguments);
			this.$el.attr('id', _.uniqueId('eac-modal-'));
			this.$el.wrapInner('<div class="eac-modal__main" role="main"></div>');
			this.$el.wrapInner('<div class="eac-modal__content" tabindex="0"></div>');
			this.$el.append('<div class="eac-modal__overlay" tabindex="-1" data-eacmodal-close></div>');
			if (this.options.autoOpen) {
				this.open();
			}
			return this;
		},

		/**
		 * Opens the modal.
		 */
		open: function () {
			this.activeElement = document.activeElement;
			$('body').css('overflow', 'hidden');
			this.$el.attr('aria-hidden', 'false');
			this.$el.addClass('is-open');
			$(document.body).append(this.$el);
			$(document.body).trigger('eac_update_ui');

			this.delegateEvents({
				...this.events,
				'keydown': 'onKeydown',
				'touchstart': 'onClick',
				'click': 'onClick',
			});

			this.setFocus();
			if (typeof this.options.onOpen === 'function') {
				this.options.onOpen.call(this);
			}
		},

		/**
		 * Closes the modal.
		 * @param {Event} e - The event object.
		 */
		close: function (e = null) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			this.$el.removeClass('is-open');
			$('body').css('overflow', '');
			this.$el.remove();
			this.$el.data('eacmodal', null);
			this.undelegateEvents();
			this.activeElement.focus();
			if (this.options.onClose) {
				this.options.onClose.call(this);
			}
		},

		/**
		 * Handles click events on the modal.
		 * @param {Event} event - The click event object.
		 */
		onClick: function (event) {
			if (
				event.target.hasAttribute(CLOSE_TRIGGER) ||
				event.target.parentNode.hasAttribute(CLOSE_TRIGGER)
			) {
				event.preventDefault();
				event.stopPropagation();
				this.close(event);
			}
		},

		/**
		 * Handles keydown events for accessibility.
		 * @param {Event} event - The keydown event object.
		 */
		onKeydown: function (event) {
			if (event.keyCode === 27) this.close(event); // esc
			if (event.keyCode === 9) this.retainFocus(event); // tab
		},

		/**
		 * Retrieves focusable elements within the modal.
		 * @returns {Array} - An array of focusable elements.
		 */
		getFocusableNodes: function () {
			return this.$el.find(FOCUSABLE_ELEMENTS.join(',')).toArray();
		},

		/**
		 * Sets focus to the first focusable element in the modal.
		 */
		setFocus: function () {
			var focusableNodes = this.getFocusableNodes();
			if (focusableNodes.length === 0) return;
			const nodesWhichAreNotCloseTargets = focusableNodes.filter(node => {
				return !node.hasAttribute(CLOSE_TRIGGER);
			});

			if (nodesWhichAreNotCloseTargets.length > 0) {
				if ($(nodesWhichAreNotCloseTargets[0]).hasClass('select2-hidden-accessible')) {
					$(nodesWhichAreNotCloseTargets[0]).select2('focus');
				} else {
					$(nodesWhichAreNotCloseTargets[0]).focus();
				}

			} else {
				$(focusableNodes[0]).focus();
			}
		},

		/**
		 * Retains focus within the modal when navigating with the Tab key.
		 * @param {Event} event - The keydown event object.
		 */
		retainFocus: function (event) {
			let focusableNodes = this.getFocusableNodes();
			if (focusableNodes.length === 0) return;
			focusableNodes = focusableNodes.filter(node => {
				return (node.offsetParent !== null);
			});
			if (!this.el.contains(document.activeElement)) {
				$(focusableNodes[0]).focus();
			} else {
				const focusedItemIndex = focusableNodes.indexOf(document.activeElement);
				if (event.shiftKey && focusedItemIndex === 0) {
					$(focusableNodes[focusableNodes.length - 1]).focus();
					event.preventDefault();
				}
				if (!event.shiftKey && focusedItemIndex === focusableNodes.length - 1) {
					$(focusableNodes[0]).focus();
					event.preventDefault();
				}
			}
		},

		/**
		 * Disables all focusable inputs within the modal.
		 */
		disableInputs: function () {
			const focusableNodes = this.getFocusableNodes();
			if (focusableNodes.length > 0) {
				const nodesWhichAreNotCloseTargets = focusableNodes.filter(node => {
					return !node.hasAttribute(CLOSE_TRIGGER);
				});

				nodesWhichAreNotCloseTargets.forEach(node => {
					$(node).attr('disabled', 'disabled').attr('data-disabled', 'true');
				});
			}
		},

		/**
		 * Enables all previously disabled inputs in the modal.
		 */
		enableInputs: function () {
			this.$el.find('[data-disabled="true"]').removeAttr('disabled').removeAttr('data-disabled');
		},

		disableSubmit: function () {
			this.$el.find('button[type="submit"]:not([disabled]):not([type="hidden"]):not([aria-hidden])').attr('disabled', 'disabled').attr('data-disabled', 'true');
		},

		enableSubmit: function () {
			this.$el.find('button[data-disabled="true"]').removeAttr('disabled').removeAttr('data-disabled');
		},
	});

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
