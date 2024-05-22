(function ($, window, document, wp, undefined) {
	'use strict';
	var $invoiceForm = $('#eac-invoice-form');

	/**
	 * Core admin functions.
	 *
	 * @since 1.0.0
	 */
	var eac_core = {
		/**
		 * Get currency.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} code - The currency code.
		 *
		 * @example
		 * eac_admin.geCurrency('USD').done(function (currency) {
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
		 * Set clipboard.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} data - The data to copy to clipboard.
		 * @param {object} $el - The jQuery element to trigger copy events on. (Default: document)
		 *
		 * @return {void}
		 */
		setClipboard: function (data, $el) {
			if ('undefined' === typeof $el) {
				$el = jQuery(document);
			}
			var $temp_input = $('<textarea style="opacity:0">');
			$('body').append($temp_input);
			$temp_input.val(data).trigger('select');
			$el.trigger('beforecopy');
			try {
				document.execCommand('copy');
				$el.trigger('aftercopy');
			} catch (err) {
				$el.trigger('aftercopyfailure');
			}
			$temp_input.remove();
		},

		/**
		 * Get form data.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to get form data.
		 * @return {object}
		 */
		getFormData: function (el) {
			var data = {};
			$(':input', $(el)).each(function () {
				var name = $(this).attr('name');
				var value = $(this).val();
				if (name) {
					data[name] = value;
				}
			});

			return data;
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
			// check if selectWoo is loaded otherwise console log a warning.
			if ('undefined' === typeof $.fn.selectWoo) {
				console.warn('SelectWoo is not loaded.');
				return;
			}

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
		 * Block form.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to block form.
		 * @return {void}
		 */
		blockForm: function (el) {
			if ('undefined' === typeof $.blockUI) {
				console.warn('jQuery BlockUI is not loaded.');
				return;
			}

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
			if ('undefined' === typeof $.blockUI) {
				console.warn('jQuery BlockUI is not loaded.');
				return;
			}
			var $form = $(el);
			$form.find(':input').prop('disabled', false);
			$form.find(':submit').removeAttr('disabled');
			$form.find('[type="button"]').removeAttr('disabled');
			$('[form="' + $form.attr('id') + '"]').removeAttr('disabled');
			$form.unblock();
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
	};

	/**
	 * Admin functions.
	 *
	 * @since 1.0.0
	 */
	var eac_admin = {
		bindEvents: function () {
			var self = this;

			// MicroModal.init();

			/**
			 * Initialize select2
			 */
			$('.eac_select2').filter(':not(.enhanced)').each(function () {
				eac_core.select2(this);
			});

			/**
			 * Initialize datepicker
			 */
			$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
				eac_core.datepicker(this);
			});

			/**
			 * Initialize tooltip
			 */
			$('.eac-tooltip').filter(':not(.enhanced)').each(function () {
				eac_core.tooltip(this);
			});

			/**
			 * Initialize number input
			 */
			$('.eac_number_input').filter(':not(.enhanced)').each(function () {
				eac_core.numberInput(this);
			});

			/**
			 * Initialize price input
			 */
			$('.eac_decimal_input').filter(':not(.enhanced)').each(function () {
				eac_core.decimalInput(this, $(this).data('currency-code'));
			});

			/**
			 * Initialize inputmask
			 */
			$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
				eac_core.inputMask(this);
			});

			/**
			 * Initialize invoice form
			 */
			$('.eac-document-form').filter(':not(.enhanced)').each(function () {
				self.initDocumentForm(this);
				$(this).addClass('enhanced');
			});

			// $('#eac-invoice-add-payment').on('click', function (e) {
			// 	e.preventDefault();
			// 	MicroModal.show('add-payment', {
			// 		onShow: function (modal) {
			// 			console.log(modal);
			// 			console.log('model opened');
			// 		},
			// 		onClose: function () {
			// 			$('#eac-invoice-form').trigger('update');
			// 		}
			// 	});
			// });

			$('form[name="eac-invoice-add-payment"]').on('change', ':input[name="account_id"]', function (e) {
				// var account_id = $(e.target).val();
				// var $form = $(e.target).closest('form');
				// var $amount = $form.find(':input[name="amount"]');
				// var $currency = $form.find(':input[name="currency_code"]');
				// var $due = $form.find(':input[name="due"]');
				// eac_admin.blockForm($form);
				// eac_admin.getAccount(account_id).done(function (account) {
				// 	eac_admin.convertCurrency($amount.data('due'), $amount.data('currency'), account.currency_code).done(function (amount) {
				// 		$amount.attr('max', amount);
				// 		$amount.val(amount);
				// 	});
				// }).always(function () {
				// 	eac_admin.unblockForm($form);
				// }).fail(function () {
				// 	$amount.removeAttr('max');
				// 	$amount.val('');
				// });
			});
		},

		/**
		 * Show flash message.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} message - The message to show.
		 * @param {string} type - The type of message. Default is 'success'.
		 * @return {void}
		 */
		flash: function (message, type) {
			type = type || 'success';
			$('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>').insertAfter('.wrap h1').delay(5000).fadeOut();
		},

		/**
		 * Process a batch action and continue until its done.
		 *
		 * @since 1.0.0
		 *
		 *
		 * @param {string} action - The action to process.
		 * @param {array} items - The items to process.
		 * @param {int} index - The index of the item to process.
		 * @param {int} total - The total number of items to process.
		 * @param {function} callback - The callback function to call when done.
		 * @return {void}
		 *
		 */
		processBatchAction: function (action, items, index, total, callback) {
			var self = this;
			var item = items[index];
			var $progress = $('.eac-batch-progress');
			var $bar = $progress.find('.progress-bar');
			var percent = Math.round((index / total) * 100);

			$bar.css('width', percent + '%');

			$.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: action,
					item: item,
					_wpnonce: eac_admin_js_vars.batch_nonce,
				},
				success: function (response) {
					if (index < total - 1) {
						self.processBatchAction(action, items, index + 1, total, callback);
					} else {
						$bar.css('width', '100%');
						$progress.fadeOut();
						callback();
					}
				},
			});
		},

		/**
		 * Initialize document form.
		 *
		 * @since 1.0.0
		 * @param {HTMLElement} el - The element to initialize document form.
		 *
		 * @return {void}
		 */
		initDocumentForm: function (el) {
			var $form = $(el);

			/**
			 * Recalculate totals.
			 *
			 * @since 1.0.0
			 * @return {void}
			 */
			function recalculateTotals() {
				var data = eac_core.getFormData($form);
				eac_core.blockForm($form);
				var action = data.action || '';
				if (!action) {
					alert('Action not defined');
					return;
				}
				action = action.replace('edit', 'calculate');
				data.action = action;
				data.calulate_totals = 'yes';

				$('.eac-document-form__body', $form).load(eac_admin_js_vars.ajax_url, data, function () {
					eac_core.unblockForm($form);
					eac_admin.bindEvents();
				});
			}

			$form.on('dirty', recalculateTotals)
				.on('change', ':input.line-item__price', recalculateTotals)
				.on('change', ':input.line-item__quantity', recalculateTotals)
				.on('change', ':input.line-item__taxes', recalculateTotals)
				.on('change', ':input#currency_code', recalculateTotals)
				.on('change', ':input#vat_exempt', recalculateTotals)
				.on('change', ':input#discount_amount', recalculateTotals)
				.on('change', ':input#discount_type', recalculateTotals)
				.on('click', '.calculate_totals', recalculateTotals)
				.on('change', ':input.add-line-item', function (e) {
					var $select = $(e.target);
					var item_id = parseInt($select.val());
					if (!item_id) {
						return false;
					}
					// now get the item details.
					eac_core.blockForm($form);
					eac_core.getItem(item_id).done(function (item) {
						const line = {
							'item_id': item.id,
							'type': item.type,
							'name': item.name,
							'description': item.description,
							'price': item.price,
							'quantity': 1,
							'units': item.unit,
							'tax_ids': item.tax_ids,
						};
						var index = $('.document-lines tr', $form).length;
						Object.keys(line).forEach(key => {
							$('.document-lines', $form).append(`<input type="hidden" name="lines[${index}][${key}]" value="${line[key]}" />`);
						});

						recalculateTotals();
					})
				})
				.on('click', '.remove-line-item', function (e) {
					e.preventDefault();
					$(e.target).closest('tr').remove();
					recalculateTotals();
				})
				.on('change', ':input#contact_id', recalculateTotals)
				.addClass('enhanced');
		},
	};

	/**
	 * Invoice functions.
	 *
	 * @type {{}}
	 */
	var eac_invoice = {};


	// Initialize admin functions.
	$(function () {
		window.eac_core || (window.eac_core = {});
		eac_admin.bindEvents();
	});

})(jQuery, window, document, wp);
