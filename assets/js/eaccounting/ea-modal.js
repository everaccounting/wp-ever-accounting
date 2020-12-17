jQuery(function ($) {
	'use strict';

	/**
	 * Ever Accounting Modal plugin
	 *
	 * @param {object} options
	 */
	$.fn.ea_modal = function (options) {
		return this.each(function () {
			new $.ea_modal(this, $.extend({}, $(this).data(), options));
		});
	};

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	var defaults = {
		title: false,
		onSubmit: false,
		onReady: false,
		submit: 'Submit',
		url: false,
	};

	/**
	 * Initialize the Modal
	 *
	 * @param template
	 * @param {object} options [description]
	 */
	$.ea_modal = function (template, options) {
		var plugin = this;
		this.template_id = template;
		this.modal_id = $(template).attr('id');
		this.$template = $(template);
		this.options = $.extend(
			{},
			defaults,
			options
		);

		var markup = '<div class="ea-modal" tabindex="-1" role="document">' +
			'<div class="ea-modal__content">' +
			'<div class="ea-modal__inner">' +
			'<header class="ea-modal__header">' +
			'<h1 class="ea-modal__title">' + this.options.title + '&nbsp;</h1>' +
			'<button class="ea-modal__close dashicons">&nbsp;</button>' +
			'</header>' +
			'<div class="ea-modal__body">' + this.$template.html() + '</div>' +
			'<div class="ea-modal__footer">' +
			'<button type="button" class="button button-secondary ea-modal__close">Cancel</button>' +
			'</div>' +
			'</div>' +
			'</div>' +
			'<div class="ea-modal__backdrop ea-modal__close">&nbsp;</div>';
		this.$modal = $(markup);
		//if form and it have submit button then remove and make one.
		this.$modal.find('form [type="submit"]').remove();
		this.$modal.find('form').append('<input type="submit" value="submit" style="display: none;">');

		// need submit then make one
		if (this.$modal.find('form') && this.options.submit) {
			var $submit = $('<button type="button" class="button button-primary">').html(this.options.submit);
			$submit.on('click', function () {
				plugin.$modal.find('form [type="submit"]').trigger('click');
			});
			this.$modal.find('.ea-modal__footer').append($submit)
		}


		this.block = function () {
			$('.ea-modal__body', plugin.$modal).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			$('.ea-modal__footer button', plugin.$modal).attr('disabled', 'disabled');
		}

		this.unblock = function () {
			$('.ea-modal__body', plugin.$modal).unblock();
			$('.ea-modal__footer button', plugin.$modal).removeAttr('disabled');
		}

		this.delegate = function () {
			$(window).bind('resize', plugin.resize);
			$(document).bind('keydown', plugin.keyboard_events);
		}

		this.undelegate = function () {
			$(window).unbind('resize', plugin.resize);
			$(document).unbind('keydown', plugin.keyboard_events);
		}

		this.resize = function () {
			// var $content = $('.ea-modal__inner', plugin.$modal);
			// var max_h = $(window).height() * 0.75;
			// $content.css({
			// 	'max-height': max_h + 'px',
			// });
		}

		$('.ea-modal__close', plugin.$modal).on('click', function (e) {
			e.preventDefault();
			plugin.close();
		});


		this.keyboard_events = function (e) {
			var button = e.keyCode || e.which;
			// Enter key
			if (13 === button && plugin.$modal.find('form').length) {
				e.preventDefault();
				plugin.$modal.find('[type="submit"]').trigger('click');
				return false;
			}
			// ESC key
			if (27 === button) {
				plugin.close();
			}
		}


		this.close = function () {
			$(document.body).trigger(
				'ea_modal_closed',
				[plugin]
			);

			$(document).off('focusin');
			$(document.body)
				.css({
					overflow: 'auto',
				})
				.removeClass('ea-modal-open');

			plugin.undelegate();

			$('.ea-modal').remove();

			$(document.body).trigger(
				'ea_modal_removed',
				[plugin]
			);
		}

		this.init = function () {
			console.log('INIT');
			$(document.body)
				.css({
					overflow: 'hidden',
				})
				.append(this.$modal)
				.addClass('ea-modal-open');

			$('.ea-modal-content', this.$modal)
				.attr('tabindex', '0')
				.focus();

			this.delegate();
			this.resize();

			$(document.body).trigger(
				'ea_modal_loaded',
				[plugin]
			);

			if (typeof this.options.onReady === 'function') {
				this.options.onReady(this);
			}

			$('form', plugin.$modal).on('submit', function (e) {
				e.preventDefault();
				plugin.block();
				const data = $('form', plugin.$modal).serializeObject();

				if (typeof plugin.options.onSubmit === 'function') {
					return plugin.options.onSubmit(data, plugin);
				} else if ($('form #action', plugin.$modal).length) {
					$.post(ajaxurl, data, function (json) {
						$.eaccounting_notice(json);
						if (json.success) {
							plugin.close();
							$(document.body).trigger(
								'ea_modal_form_submitted',
								[json, plugin, data]
							);
						}
					}).always(function (json) {
						plugin.unblock();
					});
				} else {
					console.log('ea_modal_form_submitted');
					$(document.body).trigger(
						'ea_modal_form_submitted',
						[plugin, data]
					);
				}
			});
		}

		this.init();

		return this;
	}

});
