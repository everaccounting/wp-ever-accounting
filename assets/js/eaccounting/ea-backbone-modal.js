/*global jQuery, Backbone, _ */
(function ($, Backbone, _) {
	'use strict';

	/**
	 * WooCommerce Backbone Modal plugin
	 *
	 * @param {object} options
	 */
	$.fn.ea_backbone_modal = function (options) {
		return this.each(function () {
			(new $.ea_backbone_modal($(this), options));
		});
	};

	/**
	 * Initialize the Backbone Modal
	 *
	 * @param {object} element [description]
	 * @param {object} options [description]
	 */
	$.ea_backbone_modal = function (element, options) {
		// Set settings
		var settings = $.extend({}, $.ea_backbone_modal.defaultOptions, options);
		if (settings.template) {
			new $.ea_backbone_modal.View({
				target: settings.template,
				string: settings.variable,
				onSubmit:options.onSubmit
			});
		}
	};

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	$.ea_backbone_modal.defaultOptions = {
		template: '',
		variable: {},
		onSubmit:undefined
	};

	/**
	 * Create the Backbone Modal
	 *
	 * @return {null}
	 */
	$.ea_backbone_modal.View = Backbone.View.extend({
		tagName: 'div',
		id: 'ea-backbone-modal-dialog',
		_target: undefined,
		_string: undefined,
		onSubmit: undefined,
		events: {
			'click .modal-close': 'closeButton',
			'touchstart #btn-ok': 'addButton',
			'keydown': 'keyboardActions'
		},
		resizeContent: function () {
			var $content = $('.ea-backbone-modal-content').find('article');
			var max_h = $(window).height() * 0.75;

			$content.css({
				'max-height': max_h + 'px'
			});
		},
		initialize: function (data) {
			console.log(data);
			var view = this;
			this._target = data.target;
			this._string = data.string;
			this.onSubmit = data.onSubmit;
			_.bindAll(this, 'render');
			this.render();

			$(window).resize(function () {
				view.resizeContent();
			});
		},
		render: function () {
			var template = wp.template(this._target);

			this.$el.append(
				template(this._string)
			);

			$(document.body)
				.css({
					'overflow': 'hidden'
				})
				.append(this.$el)
				.addClass('ea-modal-open');

			this.resizeContent();
			this.$('.ea-backbone-modal-content').attr('tabindex', '0').focus();

			$(document.body).trigger('init_tooltips');

			$(document.body).trigger('ea_backbone_modal_loaded', this._target);
			$(document.body).trigger(this._target + '_loaded');

			var modal = this;
			if (typeof this.onSubmit === 'function') {
				this.$el.find('form').on('submit', function(e) {
					e.preventDefault();
					modal.disableSubmit();
					modal.onSubmit(modal.getFormData(), modal);
				});
			}
		},
		closeButton: function (e) {
			e.preventDefault();
			this.closeModal();
		},
		closeModal: function () {
			$(document.body).trigger('ea_backbone_modal_before_remove', this._target);
			this.undelegateEvents();
			$(document).off('focusin');
			$(document.body).css({
				'overflow': 'auto'
			}).removeClass('ea-modal-open');
			this.remove();
			$(document.body).trigger('ea_backbone_modal_removed', this._target);
		},
		getFormData: function () {
			var data = {};
			$.each($('form', this.$el).serializeArray(), function (index, item) {
				if (item.name.indexOf('[]') !== -1) {
					item.name = item.name.replace('[]', '');
					data[item.name] = $.makeArray(data[item.name]);
					data[item.name].push(item.value);
				} else {
					data[item.name] = item.value;
				}
			});

			return data;
		},
		keyboardActions: function (e) {
			var button = e.keyCode || e.which;

			// Enter key
			if (
				13 === button &&
				!(e.target.tagName && (e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea'))
			) {
				this.addButton(e);
			}

			// ESC key
			if (27 === button) {
				this.closeButton(e);
			}
		},
		disableSubmit: function() {
			this.$el.find('*[type="submit"]').attr('disabled', 'disabled');
		},
		enableSubmit: function() {
			this.$el.find('*[type="submit"]').removeAttr('disabled');
		},
	});

}(jQuery, Backbone, _));
