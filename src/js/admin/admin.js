(function ($, window, document, wp, undefined) {
	var eac_admin = {
		init: function () {
			const self = this;

			// Initialize select2.
			$('.eac_select2').filter(':not(.enhanced)').each(function () {
				self.select2(this);
				$(this).addClass('enhanced');
			});

			// Initialize datepicker.
			$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
				self.datepicker(this);
				$(this).addClass('enhanced');
			});

			// Initialize tooltip.
			$('.eac-tooltip').filter(':not(.enhanced)').each(function () {
				self.tooltip(this);
				$(this).addClass('enhanced');
			});

			// Initialize inputmask.
			$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
				self.inputMask(this);
				$(this).addClass('enhanced');
			});

			// Initialize number input.
			$('.eac_number_input').filter(':not(.enhanced)').each(function () {
				self.numberInput(this);
				$(this).addClass('enhanced');
			});

			// Initialize decimal input.
			$('.eac_decimal_input').filter(':not(.enhanced)').each(function () {
				self.decimalInput(this);
				$(this).addClass('enhanced');
			});

			// Upload file.
			$('.eac-file-uploader').filter(':not(.enhanced)').each(function () {
				self.uploadFile(this);
				$(this).addClass('enhanced');
			});
		},

		/**
		 * Initialize select2.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize select2.
		 * @return {*|jQuery}
		 */
		select2: function (el) {
			var options = {
				allowClear: $(el).data('allow-clear') && !$(el).prop('multiple') || true,
				placeholder: $(el).data('placeholder') || '',
				width: '100%',
				minimumInputLength: $(el).data('minimum-input-length') || 0,
				readOnly: $(el).data('readonly') || false,
				ajax: {
					url: eac_admin_js_vars.ajax_url,
					dataType: 'json',
					delay: 250,
					method: 'POST',
					data: function (params) {
						return {
							term: params.term,
							action: $(el).data('action'),
							type: $(el).data('type'),
							subtype: $(el).data('subtype'),
							_wpnonce: eac_admin_js_vars.search_nonce,
							exclude: $(el).data('exclude'),
							include: $(el).data('include'),
							limit: $(el).data('limit'),
						};
					},
					processResults: function (data) {
						data.page = data.page || 1;
						return data;
					},
					cache: true
				}
			}

			// if data-action is not defined then return.
			if (!$(el).data('action')) {
				delete options.ajax;
			}

			return $(el).selectWoo(options);
		},

		/**
		 * Initialize datepicker.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize datepicker.
		 * @return {*|jQuery}
		 */
		datepicker: function (el) {
			if ('undefined' === typeof $.datepicker) {
				console.warn('jQuery UI Datepicker is not loaded.');
				return;
			}

			return $(el).datepicker({
				dateFormat: $(el).data('format') || 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				showOtherMonths: true,
				selectOtherMonths: true,
				yearRange: '-100:+10',
			});

		},

		/**
		 * Initialize tooltip.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize tooltip.
		 * @return {*|jQuery}
		 */
		tooltip: function (el) {
			if ('undefined' === typeof $.fn.tooltip) {
				console.warn('jQuery UI is not loaded.');
				return;
			}

			return $(el).tooltip({
				content: function () {
					return $(this).prop('title');
				},
				tooltipClass: 'eac-ui-tooltip',
				position: {
					my: 'center top',
					at: 'center bottom+10',
					collision: 'flipfit',
				},
				hide: {
					duration: 200,
				},
				show: {
					duration: 200,
				},
			});
		},

		/**
		 * Initialize inputmask.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize inputmask.
		 * @return {void}
		 */
		inputMask: function (el) {
			if ('undefined' === typeof $.fn.inputmask) {
				console.warn('jQuery Inputmask is not loaded.');
				return;
			}
			$(el).inputmask();
		},

		/**
		 * Block form.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to block form.
		 * @return {void}
		 */
		blockForm: function (el) {
			var $form = $(el);

			$form.find(':input').prop('disabled', true);
			$form.find(':submit').attr('disabled', 'disabled');
			$form.find(':submit').attr('disabled', 'disabled');
			$form.find('[type="button"]').attr('disabled', 'disabled');
			$('[form="' + $form.attr('id') + '"]').attr('disabled', 'disabled');

			$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		},

		/**
		 * Unblock form.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to unblock form.
		 * @return {void}
		 */
		unblockForm: function (el) {
			var $form = $(el);
			$form.find(':input').prop('disabled', false);
			$form.find(':submit').removeAttr('disabled');
			$form.find('[type="button"]').removeAttr('disabled');
			$('[form="' + $form.attr('id') + '"]').removeAttr('disabled');
			$form.unblock();
		},

		/**
		 * Initialize input number.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize input number.
		 * @return {void}
		 */
		numberInput: function (el) {
			$(el).on('input', function () {
				this.value = this.value.replace(/[^0-9]/g, '');
			});
		},

		/**
		 * Initialize decimal input.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize input price.
		 * @param {string} currency_code - The currency code.
		 *
		 * @return {void}
		 */
		decimalInput: function (el, currency_code) {
			$(el).on('input', function () {
				var val = $(this).val();
				val = val.replace(/[^0-9.]/g, '');
				$(this).val(val);
			});
		},

		/**
		 * Upload file.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to upload file.
		 * @return {void}
		 */
		uploadFile: function (el) {
			var $el = $(el);
			var $button = $el.find('.eac-file-uploader__button ');
			var $input = $el.find('.eac-file-uploader__input');
			var $filename = $el.find('.eac-file-uploader__filename');
			var $remove = $el.find('.eac-file-uploader__remove');
			var $preview = $el.find('.eac-file-uploader__preview');
			var $img = $preview.find('img');

			// WordPress media uploader.
			var frame = null;

			$button.on('click', function (e) {
				e.preventDefault();
				console.log('clicked');
				if (frame) {
					frame.open();
					return;
				}

				frame = wp.media({
					title: 'Upload File',
					button: {
						text: 'Select File'
					},
					multiple: false
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					console.log(attachment);
					$input.val(attachment.id);
					$img.attr('src', attachment.icon);
					$el.addClass('has--file');
					$filename.text(attachment.filename);
					$filename.attr('href', attachment.url);
				});

				frame.open();
			});

			$remove.on('click', function (e) {
				e.preventDefault();
				$input.val('');
				$preview.hide();
			});
		},

		/**
		 * Confirm delete action.
		 *
		 * @param {string} message  The message to confirm.
		 * @param {function} callback The callback function.
		 *
		 * @example
		 * eac_core.confirm('Are you sure you want to delete?', function () {
		 * 	console.log('Deleted');
		 * 		// Do something.
		 * 		});
		 *
		 * 	@return {void}
		 *  @since 1.0.0
		 */
		confirm: function (message, callback) {
			if (confirm(message)) {
				callback();
			}
		},
		/**
		 * Prompt.
		 *
		 *
		 * @param {string} message  The message to prompt.
		 * @param {function} callback The callback function.
		 *
		 * @example
		 * eac_core.prompt('Enter your name', function (name) {
		 * 	console.log(name);
		 * // Do something with name.
		 * });
		 *
		 * 	@return {void}
		 *  @since 1.0.0
		 */
		prompt: function (message, callback) {
			var response = prompt(message);
			if (response) {
				callback(response);
			}
		},

		/**
		 * Get currency.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} code - The currency code.
		 *
		 * @example
		 * eac_core.geCurrency('USD').done(function (currency) {
		 * 	console.log(currency);
		 * })
		 *
		 * 	@return {Promise}
		 */
		getCurrency: function (code) {
			return wp.ajax.post('eac_get_currency', {
				currency_code: code,
				_wpnonce: eac_admin_js_vars.currency_nonce,
			});
		},
		/**
		 * Get account.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The account ID.
		 *
		 * @example
		 * eac_admin.getAccount(1).then(function (account) {
		 * 	console.log(account);
		 *
		 * 		// Do something with account.
		 *
		 * });
		 *
		 * @return {Promise}
		 */
		getAccount: function (id) {
			return wp.ajax.post('eac_get_account', {
				id,
				_wpnonce: eac_admin_js_vars.account_nonce,
			});
		},

		/**
		 * Get item.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The item ID.
		 *
		 *
		 * @example
		 * eac_admin.getItem(1).then(function (item) {
		 * 	console.log(item);
		 * })
		 *
		 * 	@return {Promise}
		 *
		 */
		getItem: function (id) {
			return wp.ajax.post('eac_get_item', {
				id,
				_wpnonce: eac_admin_js_vars.item_nonce,
			});
		},

		/**
		 * Get customer.
		 *
		 * @since 1.0.0
		 *
		 * @param {integer} id - The customer ID.
		 *
		 * @example
		 * eac_admin.getCustomer(1).then(function (customer) {
		 * 	console.log
		 * 	})
		 *
		 * 	@return {Promise}
		 */
		getCustomer: function (id) {
			return wp.ajax.post('eac_get_customer', {
				id: id,
				_wpnonce: eac_admin_js_vars.customer_nonce,
			});
		},

		/**
		 * Get vendor.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The vendor ID.
		 *
		 * @example
		 * eac_admin.getVendor(1).then(function (vendor) {
		 * 	console.log(vendor);
		 * })
		 *
		 * @return {Promise}
		 */
		getVendor: function (id) {
			return wp.ajax.post('eac_get_vendor', {
				id,
				_wpnonce: eac_admin_js_vars.vendor_nonce,
			})
		},

		/**
		 * Get invoice.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The invoice ID.
		 *
		 * @example
		 * eac_admin.getInvoice(1).then(function (invoice) {
		 * 	console.log(invoice);
		 * })
		 *
		 * @return {Promise}
		 */
		getInvoice: function (id) {
			return wp.ajax.post('eac_get_invoice', {
				id,
				_wpnonce: eac_admin_js_vars.invoice_nonce,
			});
		},

		/**
		 * Get bill.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The bill ID.
		 *
		 * @example
		 * eac_admin.geBill(1).then(function (bill) {
		 * 	console.log(bill);
		 * })
		 *
		 * @return {Promise}
		 */
		getBill: function (id) {
			return wp.ajax.post('eac_get_bill', {
				id,
				_wpnonce: eac_admin_js_vars.bill_nonce,
			});
		},

		/**
		 * Get tax.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The tax ID.
		 *
		 * @example
		 * eac_admin.getTax(1).then(function (tax) {
		 * 	console.log(tax);
		 * })
		 *
		 * @return {Promise}
		 */
		getTax: function (id) {
			return wp.ajax.post('eac_get_tax', {
				id,
				_wpnonce: eac_admin_js_vars.tax_nonce,
			});
		},

		/**
		 * Convert currency.
		 *
		 * @since 1.0.0
		 * @param {string} amount - The amount to convert.
		 * @param {string} from - The currency code to convert from.
		 * @param {string} to - The currency code to convert to.
		 *
		 *
		 * @example
		 * eac_admin.convertCurrency(100, 'USD', 'EUR').then(function (amount) {
		 * 	console.log(amount);
		 * 	})
		 *
		 * 	@return {Promise}
		 */
		convertCurrency: function (amount, from, to) {
			return wp.ajax.post('eac_convert_currency', {
				amount: amount,
				from: from,
				to: to,
				_wpnonce: eac_admin_js_vars.currency_nonce,
			});
		},

		/**
		 * Get attachment.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} id - The attachment ID.
		 *
		 * @example
		 * eac_admin.getAttachment(1).then(function (attachment) {
		 * 	console.log(attachment);
		 * 		// Do something with attachment.
		 * 		})
		 *
		 * @return {Promise}
		 */
		getAttachment: function (id) {
			// use wp rest api to get attachment.
			return fetch('/wp-json/wp/v2/media/' + id)
				.then(function (response) {
					return response.json();
				})
				.catch(function (error) {
					console.error('Error:', error);
				});

		}
	}


	$(document).ready(function () {
		eac_admin.init();
	});

})(jQuery, window, document, wp);
