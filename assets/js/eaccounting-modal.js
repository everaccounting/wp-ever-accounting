/**
 * WP Ever HRM
 * https://www.pluginever.com
 *
 * Copyright (c) 2018 pluginever
 * Licensed under the GPLv2+ license.
 */

/*jslint browser: true */
/*global jQuery:false */

;(function ($, window, document, undefined) {

	// our plugin constructor
	var everModal = function (options) {
		this.options = options;
		this.$modal = $('<div>');
		this.$closeButton = $('<a class="ever-modal-close">');
		this.$body = $('body');
		this.init();
	};

	// the plugin prototype
	everModal.prototype = {
		defaults: {
			title: '',
			customClass: '',
			htmlID: 'ever-modal',
			overlayClass: '',
			closeByEscape: true,
			closeByDocument: true,
			action: 'ever_hrm_get_template',
			params: {},
			content: false,
			validate: true,
			isFullWidth: true,
			appendTo: '#wpbody-content',
			onError: false,
			onReady: false,
			onClose: false,
			onSubmit: false
		},
		init: function () {
			var self = this;
			this.options = $.extend({}, this.defaults, this.options);
			this.$appendTo = $(this.options.appendTo);

			this.build();
			return this;
		},
		isDynamic: function () {
			return !this.options.content && this.options.action;
		},
		preLoader: function () {
			return $('<div class="ever-modal-loader">\n' +
				'  <div class="ever-modal-loader-bar"></div>\n' +
				'  <div class="ever-modal-loader-bar"></div>\n' +
				'  <div class="ever-modal-loader-bar"></div>\n' +
				'  <div class="ever-modal-loader-bar"></div>\n' +
				'  <div class="ever-modal-loader-bar"></div>\n' +
				'  <div class="ever-modal-loader-ball"></div>\n' +
				'</div>');
		},
		build: function () {
			var self = this;
			if ($('.ever-modal-overlay').length === 0) {
				self.$appendTo.append('<div class="ever-modal-overlay ' + self.options.overlayClass + '"></div>');
			}

			self.$appendTo.addClass('ever-modal-container');
			//check if contents need to generate from ajax call

			if (self.isDynamic()) {
				self.$appendTo.addClass('ever-modal-loading');
				self.$appendTo.append(self.preLoader());

				window.wp.ajax.send(self.options.action, {
					data: self.options.params,
					success: function (res) {
						if (!res.template) {
							if (typeof self.options.onError === 'function') {
								self.options.onError(res);
							} else {
								self.destroy();
							}
						}

						self.showModal(res.template);

					},
					error: function (error) {

						if (typeof self.options.onError === 'function') {
							self.options.onError(error);
						} else {
							self.destroy();
						}
					}
				});
			}

		},
		showModal: function (content) {
			var self = this;

			self.$closeButton.append('<i class="fa fa-times-circle fa-3x" aria-hidden="true"></i>');
			self.$modal.append(self.$closeButton);
			//add main styling class
			self.$modal.addClass('ever-modal');

			//add custom class
			if (self.options.customClass) {
				self.$modal.addClass(self.options.customClass);
			}

			//add html id
			if (self.options.htmlID) {
				self.$modal.attr('id', self.options.htmlID);
			}

			//add title if exits
			if (self.options.title) {
				self.$modal.append($('<div class="ever-modal-header"><h2 class="ever-modal-title">' + self.options.title + '</h2></div>'));
			}



			self.$body.addClass('ever-modal-open');

			if (!self.options.isFullWidth) {
				var width = self.$appendTo.width() - (self.$appendTo.width() * 0.5);
				var height = self.$appendTo.height() - (self.$appendTo.height() * 0.5);
				self.$modal.css({
					width: width,
					height: height
				});
			}

			self.$modal.append($('<div class="ever-modal-body">' + content + '</div>'));
			self.$appendTo.removeClass('ever-modal-loading');
			self.$appendTo.addClass('ever-modal-ready');
			$('.ever-modal-loader').remove();


			if (self.options.isFullWidth) {
				self.$modal.addClass('ever-modal-fullwidth');
				self.$modal.css({
					left: $('#adminmenuwrap').width(),
					top: $('#wpadminbar').height()
				});
			}

			if (typeof self.options.onSubmit === 'function') {
				self.$modal.find('form').on('submit', function (e) {
					e.preventDefault();
					self.disableSubmit();
					self.options.onSubmit($(this), self);
				});
			}


			self.$modal.wrapInner('<div class="ever-modal-content">');
			self.$modal.wrapInner('<div class="ever-modal-inner">');

			self.$appendTo.append(self.$modal);
			self.$body.bind('keyup', {modal: self}, self.onDocumentKeyup).bind('click', {modal: self}, self.onDocumentClick);

			if (typeof self.options.onReady === 'function') {
				self.options.onReady(self, self.$modal);
			}

			$('body').trigger('ever-modal-ready', [self, self.$modal]);

		},
		disableSubmit:function(){
			this.$modal.find('*[type="submit"]').attr('disabled', 'disabled');
		},
		enableSubmit:function(){
			console.log(this.$modal);
			this.$modal.find('*[type="submit"]').removeAttr('disabled');
		},
		onDocumentKeyup: function (e) {
			var self = e.data.modal;
			if (e.keyCode === 27) {
				self.destroy();
			}
		},
		onDocumentClick: function (e) {

			var self = e.data.modal;
			if (self.options.closeByDocument) {
				if ($(e.target).is('.ever-modal-close') || $(e.target).closest('a').is('.ever-modal-close')) {
					e.preventDefault();
					self.destroy();
				}
			} else if ($(e.target).is('.ever-modal-close')) {
				e.preventDefault();
				self.destroy();
			}
		},
		destroy: function () {
			var self = this;

			if (typeof self.options.onClose === 'function') {
				if (!self.options.onClose(self)) {
					return false;
				}
			}

			setTimeout(function () {
				$('.ever-modal').remove();
				$('.ever-modal-overlay').remove();
				$('.ever-modal-loader').remove();
				$('.ever-modal-close').remove();
				$('body').removeClass('ever-modal-open');
				self.$appendTo.removeClass('ever-modal-init');
				self.$appendTo.removeClass('ever-modal-wrapper');
				self.$appendTo.removeClass('ever-modal-loading');
			}, 100);


			self.$body.unbind('keyup', self.onDocumentKeyup).unbind('click', self.onDocumentClick);

			$('body').trigger('ever-modal-closed', [self, self.$modal]);
		}
	};

	everModal.defaults = everModal.prototype.defaults;

	$.everModal = function (options) {
		new everModal(options);
	};

	window.everModal = $.everModal;

})(jQuery, window, document);
