(function ($, window, document, undefined) {

	window.eac_admin = {
		bindEvents: function () {
			var self = this;
			/**
			 * Initialize select2
			 */
			$('.eac_select2').filter(':not(.enhanced)').each(function () {
				self.initSelect2(this);
			});

			/**
			 * Initialize datepicker
			 */
			$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
				self.initDatepicker(this);
			});

			/**
			 * Initialize tooltip
			 */
			$('.eac_tooltip').filter(':not(.enhanced)').each(function () {
				self.initTooltip(this);
			});

			/**
			 * Initialize number input
			 */
			$('.eac_number_input').filter(':not(.enhanced)').each(function () {
				self.initNumberInput(this);
			});

			/**
			 * Initialize price input
			 */
			$('.eac_price_input').filter(':not(.enhanced)').each(function () {
				self.initPriceInput(this, $(this).data('currency-code'));
			});

			/**
			 * Initialize inputmask
			 */
			$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
				self.initInputmask(this);
			});

			/**
			 * Initialize invoice form
			 */
			$('#eac-invoice-form').filter(':not(.enhanced)').each(function () {
				$(this)
					.on('change', ':input#contact_id', self.invoiceForm.updateContact)
					.on('change', ':input.add-line-item', self.invoiceForm.triggerUpdate)
					.on('blur', ':input.line-item__price-input', self.invoiceForm.triggerUpdate)
					.on('blur', ':input.line-item__quantity-input', self.invoiceForm.triggerUpdate)
					.on('blur', ':input#discount_amount', self.invoiceForm.triggerUpdate)
					.on('blur', ':input#discount_type', self.invoiceForm.triggerUpdate)
					.on('select2:close', '.line-item__quantity-input', self.invoiceForm.triggerUpdate)
					.on('click', '.remove-line-item', self.invoiceForm.removeLineItem)
					.on('click', '.calculate_totals', self.invoiceForm.recalculate)
					.on('update', self.invoiceForm.recalculate)
					.addClass('enhanced');
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
		initSelect2: function (el) {
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

			return $(el).select2(options);
		},

		/**
		 * Initialize datepicker.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize datepicker.
		 * @return {*|jQuery}
		 */
		initDatepicker: function (el) {
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
		initTooltip: function (el) {
			if ('undefined' === typeof $.tooltip) {
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
		initInputmask: function (el) {
			// if ('undefined' === typeof $.inputmask) {
			// 	console.warn('jQuery Inputmask is not loaded.');
			// 	return;
			// }

			$(el).mask($(el).data('mask') || '9999-99-99');
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
		initNumberInput: function (el) {
			$(el).on('input', function () {
				this.value = this.value.replace(/[^0-9]/g, '');
			});
		},

		/**
		 * Initialize input price.
		 *
		 * @since 1.0.0
		 *
		 * @param {HTMLElement} el - The element to initialize input price.
		 * @param {string} currency_code - The currency code.
		 *
		 * @return {void}
		 */
		initPriceInput: function (el, currency_code) {
			$(el).on('input', function () {
				var val = $(this).val();
				val = val.replace(/[^0-9.]/g, '');
				$(this).val(val);
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
		 * Format address.
		 *
		 * @param {object} args - The address object.
		 * @param {string} separator - The separator.
		 *
		 * @return {string}
		 * @since 1.0.0
		 */
		formatAddress: function (args, separator) {
			separator = separator || '<br/>';
			const defaultArgs = {
				'name': '',
				'company': '',
				'address_1': '',
				'address_2': '',
				'city': '',
				'state': '',
				'postcode': '',
				'country': '',
			};
			const escapeRegExp = (string) => string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
			const format = "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}";
			args = Object.fromEntries(Object.entries(Object.assign(defaultArgs, args)).map(([key, value]) => [key, value.trim()]));
			const replace = Object.fromEntries(Object.entries({
				'{name}': args['name'],
				'{company}': args['company'],
				'{address_1}': args['address_1'],
				'{address_2}': args['address_2'],
				'{city}': args['city'],
				'{state}': args['state'],
				'{postcode}': args['postcode'],
				'{country}': country,
			}).map(([key, value]) => [key, value]));
			let formattedAddress = format;
			Object.keys(replace).forEach(key => {
				formattedAddress = formattedAddress.replace(new RegExp(escapeRegExp(key), 'g'), replace[key]);
			});
			formattedAddress = formattedAddress.replace(/  +/g, ' ').trim();
			formattedAddress = formattedAddress.replace(/\n\n+/g, "\n");
			let addressLines = formattedAddress.split("\n").map(line => line.trim()).filter(line => line);
			if (args['phone']) {
				addressLines.push('Phone: ' + args['phone']);
			}

			return addressLines.join(separator);
		},

		/**
		 * Get currency.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} code - The currency code.
		 *
		 * @example
		 * eac_admin.geCurrency('USD').then(function (currency) {
		 * 	console.log(currency);
		 * })
		 *
		 * 	@return {Promise}
		 */
		getCurrency: function (code) {
			console.log(eac_admin_js_vars);
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_currency',
					currency_code: code,
					_wpnonce: eac_admin_js_vars.currency_nonce,
				},
			});
		},

		/**
		 * Get account.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} account_id - The account ID.
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
		getAccount: function (account_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_account',
					account_id: account_id,
					_wpnonce: eac_admin_js_vars.get_account_nonce,
				},
			});
		},

		/**
		 * Get item.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} item_id - The item ID.
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
		getItem: function (item_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_item',
					item_id: item_id,
					_wpnonce: eac_admin_js_vars.item_nonce,
				},
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
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					id,
					action: 'eac_get_customer',
					_wpnonce: eac_admin_js_vars.customer_nonce,
				},
			});
		},

		/**
		 * Get vendor.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} vendor_id - The vendor ID.
		 *
		 * @example
		 * eac_admin.getVendor(1).then(function (vendor) {
		 * 	console.log(vendor);
		 * })
		 *
		 * @return {Promise}
		 */
		getVendor: function (vendor_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_vendor',
					vendor_id: vendor_id,
					_wpnonce: eac_admin_js_vars.vendor_nonce,
				},
			});
		},

		/**
		 * Get invoice.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} invoice_id - The invoice ID.
		 *
		 * @example
		 * eac_admin.getInvoice(1).then(function (invoice) {
		 * 	console.log(invoice);
		 * })
		 *
		 * @return {Promise}
		 */
		getInvoice: function (invoice_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_invoice',
					invoice_id: invoice_id,
					_wpnonce: eac_admin_js_vars.invoice_nonce,
				},
			});
		},

		/**
		 * Get bill.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} bill_id - The bill ID.
		 *
		 * @example
		 * eac_admin.geBill(1).then(function (bill) {
		 * 	console.log(bill);
		 * })
		 *
		 * @return {Promise}
		 */
		getBill: function (bill_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_bill',
					bill_id: bill_id,
					_wpnonce: eac_admin_js_vars.bill_nonce,
				},
			});
		},

		/**
		 * Get tax.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} tax_id - The tax ID.
		 *
		 * @example
		 * eac_admin.getTax(1).then(function (tax) {
		 * 	console.log(tax);
		 * })
		 *
		 * @return {Promise}
		 */
		getTax: function (tax_id) {
			return $.ajax({
				url: eac_admin_js_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'eac_get_tax',
					tax_id: tax_id,
					_wpnonce: eac_admin_js_vars.tax_nonce,
				},
			});
		},

		/**
		 * Invoice form
		 *
		 * handle invoice form events.
		 *
		 * @since 1.0.0
		 * @type {{recalculate: eac_admin.invoiceForm.recalculate}}
		 */
		invoiceForm: {
			updateContact: function (e) {
				var $form = $(e.target).closest('form');
				var $left = $(e.target).closest('.billing-fields');
				var contact_id = parseInt($(e.target).val());
				var updateBillingFields = function (contact) {
					//Find all the inputs within left that have a name attribute starting with billing_.
					$left.find(':input[name^="billing_"]').each(function () {
						var name = $(this).attr('name').replace('billing_', '');
						var value = contact[name] || '';
						// if value is a boolean then convert it to string yes or no.
						if (typeof value === 'boolean') {
							value = value ? 'yes' : 'no';
						}
						$(this).val(value);
					});
					// $form.trigger('update');
				};
				updateBillingFields({});
				if (!contact_id) {
					return;
				}
				eac_admin.blockForm($form);
				eac_admin.getCustomer(contact_id).then(function (contact) {
					eac_admin.unblockForm($form);
					updateBillingFields(contact.data || {});
				});
			},
			// updateBillingFields: function (billing) {
			// 	billing = billing || {};
			// 	$left.find(':input[name^="billing_"]').each(function () {
			// 		var name = $(this).attr('name').replace('billing_', '');
			// 		var value = contact[name] || '';
			// 		// if value is a boolean then convert it to string yes or no.
			// 		if (typeof value === 'boolean') {
			// 			value = value ? 'yes' : 'no';
			// 		}
			// 		$(this).val(value);
			// 	});
			// },
			triggerUpdate: function (e) {
				$(e.target).closest('form').trigger('update');
			},
			removeLineItem: function (e) {
				e.preventDefault();
				var $form = $(e.target).closest('form');
				$(e.target).closest('tr').remove();
				$form.trigger('update');
			},
			recalculate: function (e) {
				var $form = $(e.target).closest('form'), data = {};
				eac_admin.blockForm($form);
				$(':input', $form).each(function () {
					var name = $(this).attr('name');
					var value = $(this).val();
					if (name) {
						data[name] = value;
					}
				});

				data.action = 'eac_calculate_invoice_totals';
				$form.load(eac_admin_js_vars.ajax_url, data, function () {
					eac_admin.unblockForm($form);
					eac_admin.bindEvents();
				});
			},
		}
	};

	// eac_admin.invoiceForm = {
	// 	bindEvents: function () {
	// 		var self = this, $form = $('#eac-invoice-form');
	//
	// 		$form
	// 			.on('change', ':input.add-item', this.triggerUpdate)
	// 			.on('select2-blur', '.item-taxes', this.triggerUpdate)
	// 			.on('update', this.update);
	// 	},
	// 	triggerUpdate: function (e) {
	// 		$(e.target).closest('form').trigger('update')
	// 	},
	// 	update: function (e) {
	// 		var $form = $(e.target), data = {};
	// 		eac_admin.blockForm($form);
	// 		$(':input', $form).each(function () {
	// 			var name = $(this).attr('name');
	// 			var value = $(this).val();
	// 			if (name) {
	// 				data[name] = value;
	// 			}
	// 		});
	// 		data.action = 'eac_calculate_invoice_totals';
	//
	// 		$form.load(eac_admin_js_vars.ajax_url, data, function () {
	// 			eac_admin.unblockForm($form);
	// 			$form.removeClass('initiated');
	// 			eac_admin.bindEvents();
	// 		});
	// 	},
	// };

	$(function () {
		eac_admin.bindEvents();
		// eac_admin.invoiceForm.bindEvents();
	});

})(jQuery, window, document);

document.addEventListener( 'alpine:init', () => {
	console.log('AlpineJS Initialized');
} );
