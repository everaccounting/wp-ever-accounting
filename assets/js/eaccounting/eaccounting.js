/**
 * Ever Accounting Core Plugin
 *
 * version 1.0.0
 */
window.eaccounting = window.eaccounting || {};
/**
 * A nifty plugin to converting form to serialize object
 */
jQuery.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	jQuery.each(a, function () {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

/**
 * A plugin for converting form to serializeAssoc
 * @returns {{}}
 */
jQuery.fn.serializeAssoc = function () {
	var data = {};
	$.each(this.serializeArray(), function (key, obj) {
		var a = obj.name.match(/(.*?)\[(.*?)\]/);
		if (a !== null) {
			var subName = a[1];
			var subKey = a[2];

			if (!data[subName]) {
				data[subName] = [];
			}

			if (!subKey.length) {
				subKey = data[subName].length;
			}

			if (data[subName][subKey]) {
				if ($.isArray(data[subName][subKey])) {
					data[subName][subKey].push(obj.value);
				} else {
					data[subName][subKey] = [];
					data[subName][subKey].push(obj.value);
				}
			} else {
				data[subName][subKey] = obj.value;
			}
		} else {
			if (data[obj.name]) {
				if ($.isArray(data[obj.name])) {
					data[obj.name].push(obj.value);
				} else {
					data[obj.name] = [];
					data[obj.name].push(obj.value);
				}
			} else {
				data[obj.name] = obj.value;
			}
		}
	});
	return data;
};

/**
 * Select2 wrapper for EverAccounting
 *
 * The plugin is created to handle ajax search
 * & create item on the fly
 *
 * @since 1.0.2
 */

jQuery(function ($) {
	$.fn.eaccounting_select2 = function (options) {
		return this.each(function () {
			new $.eaccounting_select2(this, options);
		});
	};
	$.eaccounting_select2 = function (el, options) {
		this.el = el;
		this.$el = $(el);
		this.placeholder = this.$el.attr('placeholder');
		this.template = this.$el.attr('data-template');
		this.nonce = this.$el.attr('data-nonce');
		this.type = this.$el.attr('data-type');
		this.creatable_text = this.$el.attr('data-text');
		this.creatable = this.$el.is('[data-creatable]');
		this.creatable = this.creatable === true;
		this.ajax = this.$el.is('[data-ajax]');
		this.ajax = this.ajax === true;
		this.id = this.$el.attr('id');
		if (this.ajax && (!this.type || !this.nonce)) {
			console.warn('ajax type defined without nonce and data type');
			this.ajax = false;
		}

		if (this.creatable && !this.template) {
			console.warn('modal type defined without template');
			this.creatable = false;
		}
		var self = this;
		var data = {};
		data.placeholder = this.placeholder;
		data.allowClear = false;
		if (this.ajax) {
			data.ajax = {
				cache: true,
				delay: 500,
				url: eaccounting_i10n.ajaxurl,
				method: 'POST',
				dataType: 'json',
				data: function (params) {
					return {
						action: 'eaccounting_dropdown_search',
						nonce: self.nonce,
						type: self.type,
						search: params.term,
						page: params.page,
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.results,
						pagination: {
							more: data.pagination.more,
						},
					};
				},
			};
		}

		var settings = $.extend({}, data, options);

		this.$el.select2(settings);

		if (this.creatable && self.template) {
			this.$el.on('select2:open', function (e) {
				var $results = $('#select2-' + self.id + '-results').closest('.select2-results');
				if (!$results.children('.ea-select2-footer').length) {
					var $footer = $(
						'<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus"></span>' +
							self.creatable_text +
							'</a>'
					).on('click', function (e) {
						e.preventDefault();
						self.$el.select2('close');
						console.log(self.template);
						$(document).trigger('ea_trigger_creatable', [self.$el, self.template]);
					});
					$results.append($footer);
				}
			});
		}

		return this.$el;
	};

	$('.ea-select2').eaccounting_select2();
});

/**
 * Color field wrapper for Ever Accounting
 * @since 1.0.2
 */
jQuery(function ($) {
	jQuery.fn.ea_color_picker = function () {
		return this.each(function () {
			var el = this;
			$(el)
				.iris({
					change: function (event, ui) {
						$(el).parent().find('.colorpickpreview').css({ backgroundColor: ui.color.toString() });
					},
					hide: true,
					border: true,
				})
				.on('click focus', function (event) {
					event.stopPropagation();
					$('.iris-picker').hide();
					$(el).closest('div').find('.iris-picker').show();
					$(el).data('original-value', $(el).val());
				})
				.on('change', function () {
					if ($(el).is('.iris-error')) {
						var original_value = $(this).data('original-value');

						if (original_value.match(/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/)) {
							$(el).val($(el).data('original-value')).change();
						} else {
							$(el).val('').change();
						}
					}
				});

			$('body').on('click', function () {
				$('.iris-picker').hide();
			});
		});
	};

	$('.ea-input-color').ea_color_picker();
});

/**
 * Redirect plugin for EverAccounting.
 * @since 1.0.2
 */
jQuery(function ($) {
	$.eaccounting_redirect = function (url) {
		if ('object' === typeof url) {
			if (!('redirect' in url)) {
				return;
			}
			url = url.redirect;
		}

		if (!url) {
			return;
		}
		url = url.trim();
		if (!url) {
			return false;
		}
		var ua = navigator.userAgent.toLowerCase(),
			isIE = ua.indexOf('msie') !== -1,
			version = parseInt(ua.substr(4, 2), 10);

		// Internet Explorer 8 and lower
		if (isIE && version < 9) {
			var link = document.createElement('a');
			link.href = url;
			document.body.appendChild(link);
			return link.click();
		}
		// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
		window.location.href = url;
	};

	$.fn.eaccounting_redirect = function (url) {
		return new $.eaccounting_redirect(url);
	};
});

/**
 *
 */
jQuery(function ($) {
	$.fn.eaccounting_creatable = function (options) {
		return this.each(function () {
			new $.eaccounting_creatable(this, options);
		});
	};

	$.eaccounting_creatable = function (el, options) {
		this.defaults = {
			option: function (item) {
				return { id: item.id, text: item.name };
			},
			template: undefined,
			onReady: undefined,
			onSubmit: undefined,
		};
		this.el = el;
		this.$el = $(el);
		this.options = $.extend(this.defaults, options);
		var self = this;
		this.block = function () {
			self.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		};

		this.unblock = function () {
			self.$el.unblock();
		};

		this.onError = function (error) {
			console.warn(error);
			self.unblock();
		};

		this.init_plugins = function () {
			$('.ea-select2', this.$el).eaccounting_select2();
			$('.ea-input-color').ea_color_picker();
		};

		this.handleSubmit = function (formData, $modal) {
			self.block();
			$modal.disableSubmit();

			if (typeof self.options.onSubmit === 'function') {
				self.options.onSubmit(self.$el, formData, $modal);
			}

			wp.ajax.send({
				data: formData,
				success: function (res) {
					var option = self.options.option(res.item);
					self.$el.eaccounting_select2({ data: [option] });
					self.$el.val(option.id).trigger('change');
					$.eaccounting_notice(res.message, 'success');
					$modal.closeModal();
					$modal.enableSubmit();
				},
				error: function (error) {
					$.eaccounting_notice(error.message, 'error');
					$modal.enableSubmit();
				},
			});
		};
		this.handleModal = function (e, $el, template) {
			e.preventDefault();
			if ($el.is(self.$el)) {
				e.preventDefault();
				$(this).ea_backbone_modal({
					template: 'ea-modal-' + template,
					onSubmit: self.handleSubmit,
					onReady: function (el, $modal) {
						if (typeof self.options.onReady === 'function') {
							self.options.onReady(self.$el, $modal, self);
						}
					},
				});
			}
		};

		this.init = function () {
			$(document).on('ea_trigger_creatable', self.handleModal).on('ea_backbone_modal_loaded', self.init_plugins);
		};

		this.init();

		return this;
	};
});

jQuery(function ($) {
	$.fn.eaccounting_form = function (options) {
		return this.each(function () {
			new $.eaccounting_form(this, options);
		});
	};

	$.eaccounting_form = function (el, options) {
		var form = {};
		form.el = el;
		form.$el = $(el);
		form.options = options;

		//fields
		form.account_id = $('#account_id, #from_account_id', form.$el);
		form.currency_code = $('#currency_code', form.$el);
		form.amount = $('#amount, #opening_balance', form.$el);
		form.code = $('#code', form.$el);
		form.files = $('.ea-files-preview', form.$el);
		form.uploader = $('.ea-files-upload', form.$el);
		form.block = function () {
			form.$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		};

		form.unblock = function () {
			form.$el.unblock();
		};

		form.onError = function (error) {
			console.warn(error);
			form.unblock();
		};

		form.maskAmount = function (currency) {
			form.amount.inputmask('decimal', {
				alias: 'numeric',
				groupSeparator: currency.thousand_separator,
				autoGroup: true,
				digits: currency.precision,
				radixPoint: currency.decimal_separator,
				digitsOptional: false,
				allowMinus: false,
				prefix: currency.symbol,
				placeholder: '0.000',
				rightAlign: 0,
			});
		};

		form.getCurrency = function (code, onSuccess, onError) {
			wp.ajax.send('eaccounting_get_currency', {
				data: {
					code: code,
					_wpnonce: eaccounting_i10n.nonce.get_currency,
				},
				success: onSuccess,
				error: onError,
			});
		};

		form.getAccount = function (id, onSuccess, onError) {
			wp.ajax.send('eaccounting_get_account', {
				data: {
					id: id,
					_wpnonce: eaccounting_i10n.nonce.get_account,
				},
				success: onSuccess,
				error: onError,
			});
		};

		//bind events
		form.$el.on('submit', function (e) {
			e.preventDefault();
			form.block();
			// var checkboxes = $('input:checkbox', form.$el).map(function() {
			// 	return { name: this.name, value: this.checked ? this.value : "false" };
			// });
			//
			wp.ajax.send({
				data: form.$el.serializeObject(),
				success: function (res) {
					form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		});

		//on currency change
		form.currency_code.on('change', function () {
			if (form.amount.length) {
				var code = form.currency_code.val();
				form.block();
				form.getCurrency(
					code,
					function (res) {
						form.maskAmount(res);
						form.unblock();
					},
					form.onError
				);
			}
		});

		//on account change
		form.account_id.on('change', function () {
			if (form.amount.length) {
				var account_id = form.account_id.val();
				var id = parseInt(account_id, 10);
				if (!id) {
					return;
				}
				form.block();
				form.getAccount(
					id,
					function (res) {
						form.getCurrency(
							res.currency_code,
							function (code) {
								form.maskAmount(code);
								form.unblock();
							},
							form.onError
						);
					},
					form.onError
				);
			}
		});

		//on code change
		form.code.on('change', function () {
			var code = form.code.val();
			console.log(code);
			if (!code) {
				return false;
			}
			try {
				currency = eaccounting_i10n.global_currencies[code];
				$('#precision', form.$el).val(currency.precision).change();
				$('#position', form.$el).val(currency.position).change();
				$('#symbol', form.$el).val(currency.symbol).change();
				$('#decimal_separator', form.$el).val(currency.decimal_separator).change();
				$('#thousand_separator', form.$el).val(currency.thousand_separator).change();
			} catch (e) {
				console.warn(e.message);
			}
		});

		form.uploader.on('change', function (e) {
			var data = new FormData();
			data.append('nonce', $(this).data('nonce'));
			data.append('upload', $(this)[0].files[0]);
			data.append('limit', $(this).data('limit'));
			data.append('action', 'eaccounting_upload_files');
			form.block();
			window.wp.ajax.send({
				type: 'POST',
				data: data,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function (res) {
					var item = $('<li>').append('<a href="' + res.url + '" target="_blank">' + res.name + '</a>');
					item.append('<a href="#" class="delete"><span class="dashicons dashicons-no-alt"></span></a>');
					form.files.append(item);
					form.unblock();
				},
				error: function (error) {
					form.unblock();
				},
			});
		});

		//change on first load
		form.account_id.trigger('change');
		form.currency_code.trigger('change');
	};
});
