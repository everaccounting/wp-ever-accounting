/* global eac_admin_vars, eac_currencies, eac_base_currency */
import Money from '@eac/money';

/**
 * ========================================================================
 * ADMIN UI
 * ========================================================================
 */
jQuery(document).ready(($) => {
	'use strict';

	var initializeUI = function () {
		// Select2.
		$('.eac_select2').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				allowClear: $this.data('allow-clear') && !$this.prop('multiple') || true,
				placeholder: $this.data('placeholder') || '',
				width: '100%',
				minimumInputLength: $this.data('minimum-input-length') || 0,
				readOnly: $this.data('readonly') || false,
				ajax: {
					url: eac_admin_vars.ajax_url,
					dataType: 'json',
					delay: 250,
					method: 'POST',
					data: function (params) {
						return {
							term: params.term,
							action: $this.data('action'),
							type: $this.data('type'),
							subtype: $this.data('subtype'),
							_wpnonce: eac_admin_vars.search_nonce,
							exclude: $this.data('exclude'),
							include: $this.data('include'),
							limit: $this.data('limit'),
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
			if (!$this.data('action')) {
				delete options.ajax;
			}
			$this.addClass('enhanced').selectWoo(options);
		});

		// Datepicker.
		$('.eac_datepicker').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				dateFormat: $this.data('format') || 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				showOtherMonths: true,
				selectOtherMonths: true,
				yearRange: '-100:+10',
			};
			$this.addClass('enhanced').datepicker(options);
		});

		// Tooltip.
		$('.eac_tooltip').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				position: {
					my: 'center bottom-15',
					at: 'center top',
				},
				tooltipClass: 'eac_tooltip',
			};
			$this.addClass('enhanced').tooltip(options);
		});

		// currency.
		$(':input.eac_amount').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const currency = $this.data('currency') || eac_admin_vars.base_currency;
			const precision = eac_admin_vars.currencies[currency].precision || 2;
			const symbol = eac_admin_vars.currencies[currency].symbol || '';
			const position = eac_admin_vars.currencies[currency].position || 'before';
			$this.inputmask({
				alias: 'currency',
				placeholder: '0.00',
				rightAlign: false,
				allowMinus: true,
				digits: precision,
				prefix: 'before' === position ? symbol : '',
				suffix: 'after' === position ? symbol : '',
				removeMaskOnSubmit: true
			}).addClass('enhanced');
		});

		// inputMask.
		$('.eac_inputmask').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			const options = {
				alias: $this.data('alias') || '',
				placeholder: $this.data('placeholder') || '',
				clearIncomplete: $this.data('clear-incomplete') || false,
			};
			$this.addClass('enhanced').inputmask(options);
		});

		// Number Input.
		$('.eac_number_input').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			$this.addClass('enhanced').on('input', function () {
				$this.value = $this.value.replace(/[^0-9]/g, '');
			});
		});

		// Decimal Input.
		$('.eac_decimal_input').filter(':not(.enhanced)').each(function () {
			const $this = $(this);
			$this.addClass('enhanced').on('input', function () {
				var val = $(this).val();
				val = val.replace(/[^0-9.]/g, '');
				$this.val(val);
			});
		});

		// Polyfill for card padding for firefox.
		$('.eac-card').each(function () {
			if (!$(this).children('[class*="eac-card__"]').length && !parseInt($(this).css('padding'))) {
				$(this).css('padding', '8px 12px');
			}
		});
	}

	// Initialize UI.
	initializeUI();

	// Reinitialize UI when document body triggers 'eac-update-ui'.
	$(document.body).on('eac_update_ui', initializeUI);

	// Media Uploader.
	$('.eac-file-upload').filter(':not(.enhanced)').each(function () {
		const $this = $(this);
		const $button = $this.find('.eac-file-upload__button');
		const $value = $this.find('.eac-file-upload__value');
		const $preview = $this.find('.eac-file-upload__icon img');
		const $name = $this.find('.eac-file-upload__name a');
		const $size = $this.find('.eac-file-upload__size');
		const $remove = $this.find('a.eac-file-upload__remove');

		$button.on('click', function (e) {
			e.preventDefault();
			const frame = wp.media({
				title: $button.data('uploader-title'),
				multiple: false
			});
			frame.on('ready', function () {
				frame.uploader.options.uploader.params = {
					type: 'eac_file',
				};
			});
			frame.on('select', function () {
				const attachment = frame.state().get('selection').first().toJSON();
				const src = attachment.type === 'image' ? attachment.url : attachment.icon;
				$value.val(attachment.id);
				$preview.attr('src', src).show();
				$preview.attr('alt', attachment.filename);
				$name.text(attachment.filename).attr('href', attachment.url);
				$size.text(attachment.filesizeHumanReadable);
				$remove.show();
				$this.addClass('has--file');
			});
			frame.open();
		});

		$remove.on('click', function (e) {
			e.preventDefault();
			$this.removeClass('has--file');
			$value.val('');
			$preview.attr('src', '').hide();
			$name.text('').attr('href', '');
			$size.text('');
		});
	});
});

/**
 * ========================================================================
 * SETTINGS UI
 * ========================================================================
 */
jQuery(document).ready(function ($) {
	'use strict';
	$('body').on('click', 'input#eac_business_logo', function (e) {
		e.preventDefault();
		var $this = $(this);

		const frame = wp.media({
			multiple: false,
			// images only.
			library: {type: 'image'},
		});

		frame.on('select', function () {
			const attachment = frame.state().get('selection').first().toJSON();
			$this.val(attachment.url);
		});

		frame.open();
	});

	$('.ea-financial-start').datepicker({dateFormat: 'dd-mm'});

	$('.eac-exchange-rates')
		.on('click', 'a.add', function (e) {
			e.preventDefault();
			$(this)
				.closest('table')
				.find('tbody')
				.append($(this).data('row'))
		})
		.on('change', 'select', function (e) {
			e.preventDefault();
			$(this)
				.closest('tr')
				.find('input, select')
				.each(function () {
					var $this = $(this);
					$this.attr(
						'name',
						$this.attr('name').replace(/\[.*\]/, '[' + e.target.value + ']')
					);
				});
		})
		.on('click', 'a.remove', function (e) {
			e.preventDefault();
			$(this).closest('tr').remove();
		});
});

/**
 * ========================================================================
 * SALES UI
 * ========================================================================
 */
jQuery(document).ready(function ($) {
	'use strict';

	$('#eac-payment-form').eac_form({
		events: {
			'change :input[name="account_id"]': 'handleExchangeRate',
		},
		handleExchangeRate: function () {
			var self = this;
			var $amount = this.$(':input[name="amount"]');
			var $conversion = this.$(':input[name="exchange_rate"]');
			var account_id = this.$(':input[name="account_id"]').val();

			if (!account_id) {
				$conversion.val(1.00);
				$conversion.attr('readonly', true).val(1.00);
				return;
			}

			self.block();
			var Account = new eac_api.Account({id: account_id});
			Account.fetch({
				success: function (account) {
					var new_currency = account.get('currency') || eac_base_currency;
					$amount.data('currency', new_currency).removeClass('enhanced');
					$conversion.val(eac_currencies[new_currency].rate || 1.00);
					$conversion.attr('readonly', new_currency === eac_base_currency);
					$(document.body).trigger('eac_update_ui');
				}
			}).then(function () {
				self.unblock();
			});
		},
	});

	/**
	 * Invoice Form.
	 *
	 * @constructor
	 */
	function Invoice_Form(el) {
		var form = this;

		/**
		 * Recipient view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.BillingAddr = wp.Backbone.View.extend({
			el: '.billing-address',

			template: wp.template('eac-invoice-billing-addr'),

			initialize() {
				const {state} = this.options;
				this.listenTo(state, 'change:contact_id', this.render);
			},

			prepare() {
				const {state} = this.options;

				return {
					...state.toJSON(),
				}
			},
		});

		/**
		 * Invoice item view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Item = wp.Backbone.View.extend({
			tagName: 'tr',

			className: 'eac-document-items__item',

			template: wp.template('eac-invoice-item'),

			events: {
				'change .item-quantity': 'onQuantityChange',
				'change .item-price': 'onPriceChange',
				'select2:select .item-taxes': 'onAddTax',
				'select2:unselect .item-taxes': 'onRemoveTax',
				'click .remove-item': 'onRemoveLineItem',
			},

			initialize() {
				const {state} = this.options;
				this.listenTo(this.model, 'change', this.render);
				this.listenTo(this.model, 'change', this.render);
				this.listenTo(this.model.get('taxes'), 'add remove change', this.render);
				this.listenTo(state, 'change:currency', this.render);
			},

			prepare() {
				const {model, state} = this.options;
				const data = model.toJSON();
				return {
					...data,
					formatted_subtotal: state.get('money').format(data.subtotal),
					formatted_tax: state.get('money').format(data.tax),
					tax: model.get('taxes').reduce((acc, tax) => acc + tax.get('amount'), 0),
					taxes: data.taxes?.toJSON(),
				}
			},

			render() {
				console.log('=== Invoice.Item.render() ===');
				wp.Backbone.View.prototype.render.apply(this, arguments);
				$(document.body).trigger('eac_update_ui');
				return this;
			},

			onQuantityChange(e) {
				e.preventDefault();
				var value = parseFloat(e.target.value, 10);
				if (!value) {
					this.onRemoveLineItem(e);
					return;
				}
				this.model.set('quantity', value);
				this.options.state.updateAmounts();
			},

			onPriceChange(e) {
				e.preventDefault();
				var value = parseFloat(e.target.value, 10);
				this.model.set('price', value);
				this.options.state.updateAmounts();
			},

			onAddTax(e) {
				e.preventDefault();
				var data = e.params.data;
				var tax_id = parseInt(data.id, 10) || null;
				if (tax_id) {
					// any of the taxes already exists with the same tax_id then skip.
					if (this.model.get('taxes').findWhere({tax_id})) {
						return;
					}

					var tax = new eac_api.Tax({id: tax_id});
					tax.fetch({
						success: (model) => {
							this.model.get('taxes').add({
								...model.toJSON(),
								tax_id: model.get('id'),
								id: _.uniqueId('tax_'),
							});
							this.options.state.updateAmounts();
						}
					});
				}
			},

			onRemoveTax(e) {
				e.preventDefault();
				var data = e.params.data;
				var tax_id = parseInt(data.id, 10) || null;
				if (tax_id) {
					var tax = this.model.get('taxes').findWhere({tax_id: tax_id});
					if (tax) {
						this.model.get('taxes').remove(tax);
						this.options.state.updateAmounts();
					}
				}
			},

			onRemoveLineItem(e) {
				e.preventDefault();
				this.options.state.get('items').remove(this.model);
				this.options.state.updateAmounts();
			}
		});

		/**
		 * Invoice No Items view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.NoItems = wp.Backbone.View.extend({
			tagName: 'tr',

			className: 'eac-document-items__no-items',

			template: wp.template('eac-invoice-empty'),
		});

		/**
		 * Invoice Items view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Items = wp.Backbone.View.extend({
			tagName: 'tbody',

			className: 'eac-document-items__items',

			initialize() {
				const {state} = this.options;
				this.listenTo(state.get('items'), 'add', this.render);
				this.listenTo(state.get('items'), 'remove', this.render);
				this.listenTo(state.get('items'), 'add', this.scrollToBottom);
			},

			render() {
				this.views.detach();
				const {state} = this.options;
				const items = state.get('items');
				if (!items.length) {
					this.views.add(new form.NoItems(this.options));
				} else {
					items.each((model) => {
						this.views.add(new form.Item({...this.options, model}));
					});
				}
				$(document.body).trigger('eac_update_ui');
				return this
			},

			scrollToBottom() {
				var $el = this.$el.closest('tbody').find('tr:last-child');
				$el.find('.item-price').focus();
				// Now we need to scroll to the bottom of the table.
				var $table = this.$el.closest('table');
				$('html, body').animate({
					scrollTop: $el.offset().top - $table.offset().top + $table.scrollTop()
				}, 500);
			}
		});

		/**
		 * Invoice Toolbar view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Toolbar = wp.Backbone.View.extend({
			tagName: 'tbody',

			className: 'eac-document-items__toolbar',

			template: wp.template('eac-invoice-toolbar'),

			events: {
				'select2:select .add-item': 'onAddItem',
			},

			prepare() {
				const {state} = this.options;
				return {
					...state.toJSON(),
				}
			},

			onAddItem(e) {
				e.preventDefault();
				const {state} = this.options;
				const item_id = parseInt(e.params.data.id, 10) || null;
				if (item_id) {
					$(e.target).val(null).trigger('change');
					new eac_api.Item({id: item_id}).fetch().then(json => {
						const taxes = json.taxes || [];
						json.taxes = new eac_api.DocumentTaxes();
						json.taxes.add(taxes.map(tax => ({
							...tax,
							id: _.uniqueId('tax_'),
							rate: tax.rate,
							tax_id: tax.id,
							amount: 0,
						})));
						state.get('items').add({
							...json,
							id: _.uniqueId('item_'),
							price: (json.price || 0) * state.get('exchange_rate'),
							quantity: 1,
							item_id: json.id,
						});
						state.updateAmounts();
					})
				}
			},
		});

		/**
		 * Invoice Totals view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Totals = wp.Backbone.View.extend({
			tagName: 'tfoot',

			className: 'eac-document-items__totals',

			template: wp.template('eac-invoice-totals'),

			events: {
				'change [name="discount_value"]': 'onDiscountValueChange',
				'change [name="discount_type"]': 'onDiscountTypeChange',
			},

			initialize() {
				const {state} = this.options;
				this.listenTo(state, 'change:currency', this.render);
				this.listenTo(state, 'change', this.render);
			},

			prepare() {
				const {state} = this.options;
				return {
					...state.toJSON(),
					formatted_subtotal: state.get('money').format(state.get('subtotal')),
					formatted_discount: state.get('money').format(state.get('discount')),
					formatted_tax: state.get('money').format(state.get('tax')),
					formatted_total: state.get('money').format(state.get('total')),
				}
			},
			onDiscountValueChange(e) {
				e.preventDefault();
				var state = this.options.state;
				var value = parseFloat(e.target.value, 10);
				state.set('discount_value', value);
				state.updateAmounts();
			},

			onDiscountTypeChange(e) {
				var state = this.options.state;
				var value = e.target.value;
				state.set('discount_type', value);
				state.updateAmounts();
			}
		});

		/**
		 * Invoice Main view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Main = wp.Backbone.View.extend({
			el: el,

			events: {
				'change [name="contact_id"]': 'onChangeContact',
				'change :input[name="currency"]': 'onChangeCurrency',
				'change :input[name="exchange_rate"]': 'onChangeExchangeRate',
				'select2:select .add-item': 'onAddItem',
			},

			render: function () {
				this.views.detach();
				this.views.add('.billing-address', new form.BillingAddr(this.options));
				this.views.add('table.eac-document-items', new form.Items(this.options));
				this.views.add('table.eac-document-items', new form.Toolbar(this.options));
				this.views.add('table.eac-document-items', new form.Totals(this.options));
				$(document.body).trigger('eac_update_ui');
				return this;
			},
			BlockUnblockUI: function () {
				if (is_busy && !this.$el.find('.blockUI').length) {
					// Ensure position is relative
					if (this.$el.css('position') === 'static') {
						this.$el.css('position', 'relative');
					}

					// Create overlay
					$('<div class="blockUI"></div>').css({
						position: 'absolute',
						top: 0,
						left: 0,
						width: '100%',
						height: '100%',
						backgroundColor: 'rgb(255, 255, 255)',
						opacity: 0.1,
						cursor: 'wait',
						zIndex: 9999
					}).appendTo(this.$el);
				} else {
					this.$el.find('.blockUI').remove();
				}
			},
			onChangeContact: function (e) {
				e?.preventDefault();
				const {state} = this.options;
				const json = $(e.target).select2('data')?.[0];
				const dataToSet = Object.keys(state.toJSON())
					.filter(key => key.startsWith('contact_'))
					.reduce((acc, key) => {
						acc[key] = json && json.hasOwnProperty(key.slice(8)) ? json[key.slice(8)] : '';
						return acc;
					}, {});

				state.set(dataToSet);
			},
			onChangeCurrency(e) {
				e?.preventDefault();
				var self = this;
				var $exchange = this.$(':input[name="exchange_rate"]');
				var currency = this.$(':input[name="currency"]').val();
				self.options.state.set('money', new Money(currency));
				if (currency) {
					self.options.state.set('currency', currency);
					$exchange.val(eac_currencies[currency].rate || 1.00).trigger('change');
					$exchange.attr('readonly', currency === eac_base_currency);
				}
			},
			onChangeExchangeRate(e) {
				e?.preventDefault();
				var self = this;
				var $exchange = this.$(':input[name="exchange_rate"]');
				var rate = parseFloat($exchange.val(), 10);
				if (rate) {
					self.options.state.set('exchange_rate', rate);
				}
			},
		});

		/**
		 * Invoice state model.
		 *
		 * @type {Backbone.Model}
		 * @since 1.0.0
		 */
		this.State = eac_api.Invoice.extend({});

		/**
		 * Initialize Invoice.
		 *
		 * @since 1.0.0
		 * @return {void}
		 */
		this.Init = function () {
			const currency = eac_invoice_vars?.currency || eac_base_currency;
			// create new invoice state.
			var state = new this.State({
				...window.eac_invoice_vars || {},
				money: new Money(currency),
			});
			state.set('items', new eac_api.DocumentItems());

			// Hydrate collections.
			var items = eac_invoice_vars?.items || [];
			items.forEach(function (_item) {
				var taxes = _item.taxes || [];
				var item = new eac_api.DocumentItem(_item);
				item.set('taxes', new eac_api.DocumentTaxes());
				taxes.forEach(function (tax) {
					item.get('taxes').add(tax);
				});
				state.get('items').add(item);
			});

			return new this.Main({state}).render();
		}

		if ($(el).length) {
			this.Init();
		}

		return this;
	}

	// Initialize Invoice.
	new Invoice_Form('#eac-invoice-form');
});

/**
 * ========================================================================
 * PURCHASES UI
 * ========================================================================
 */
jQuery(document).ready(function ($) {
	'use strict';

	$('#eac-expense-form').eac_form({
		events: {
			'change :input[name="account_id"]': 'handleExchangeRate',
		},
		handleExchangeRate: function () {
			var self = this;
			var $amount = this.$(':input[name="amount"]');
			var $conversion = this.$(':input[name="exchange_rate"]');
			var account_id = this.$(':input[name="account_id"]').val();

			if (!account_id) {
				$conversion.val(1.00);
				$conversion.attr('readonly', true).val(1.00);
				return;
			}

			self.block();
			var Account = new eac_api.Account({id: account_id});
			Account.fetch({
				success: function (account) {
					var new_currency = account.get('currency') || eac_base_currency;
					$amount.data('currency', new_currency).removeClass('enhanced');
					$conversion.val(eac_currencies[new_currency].rate || 1.00);
					$conversion.attr('readonly', new_currency === eac_base_currency);
					$(document.body).trigger('eac_update_ui');
				}
			}).then(function () {
				self.unblock();
			});
		},
	});

	var Bill = {};

	Bill.State = Backbone.Model.extend({
		defaults: {
			id: null,
			contact_id: null,
			currency: 'USD',
			exchange_rate: 1.00,
			contact_name: null,
			contact_email: null,
			contact_phone: null,
			contact_address: null,
			contact_city: null,
			contact_state: null,
			contact_zip: null,
			contact_country: null,
			contact_tax_number: null,
			items: [],
		}
	});

	Bill.Form = wp.Backbone.View.extend({
		el: '#eac-bill-form',

		events: {
			'change :input[name="currency"]': 'onChangeCurrency',
			'change :input[name="contact_id"]': 'onChangeContact',
			'select2:select .add-item': 'onAddItem',
		},

		initialize: function () {
			const {state} = this.options;
			this.listenTo(state, 'change:contact_id', this.renderAddress);
			this.listenTo(state, 'change:items', this.renderItems);
			this.listenTo(state, 'add:items', this.renderItems);
			wp.Backbone.View.prototype.initialize.apply(this, arguments);
		},

		render: function () {
			this.views.detach();
			this.renderAddress();
			this.renderItems();
			this.renderToolbar();
			this.renderTotals();
			$(document.body).trigger('eac_update_ui');
			return this;
		},
		renderAddress: function () {
			const {state} = this.options;
			const template = wp.template('eac-address');
			this.$('.billing-address').html(template(state.toJSON()));

			return this;
		},
		renderItems: function () {
			const {state} = this.options;
			const items = state.get('items');
			const empty_template = wp.template('eac-empty');
			if (!items.length) {
				this.$('tbody.eac-document-items__items').html(empty_template());
			} else {
				const template = wp.template('eac-item');
				items.each((model) => {
					this.$('tbody.eac-document-items__items').append(template(model.toJSON()));
				});

				state.trigger('change');
			}

			return this;
		},
		renderToolbar: function () {
			const template = wp.template('eac-toolbar');
			this.$('tbody.eac-document-items__toolbar').html(template());

			return this;
		},
		renderTotals: function () {
		},
		onChangeCurrency: function () {
			var self = this;
			var $conversion = this.$(':input[name="exchange_rate"]');
			var currency = this.$(':input[name="currency"]').val();

			self.block();
			$conversion.val(eac_currencies[currency].rate || 1.00).trigger('change');
			$conversion.attr('readonly', currency === eac_base_currency);
			self.unblock();
		},
		onChangeContact: function () {
			var self = this;
			var {state} = this.options;
			var $contact = this.$(':input[name="contact_id"]');
			var contact_id = $contact.val();

			// bail if no contact selected.
			if (!contact_id) {
				// replace all contact fields with empty values.
				const data = Object.keys(state.toJSON())
					.filter(key => key.startsWith('contact_'))
					.reduce((acc, key) => {
						acc[key] = '';
						return acc;
					}, {});
				state.set(data);
				state.trigger('change:contact');
				return;
			}

			self.block();
			var Contact = new eac_api.Vendor({id: contact_id});
			Contact.fetch().then(function (json) {
				const data = Object.keys(json)
					.filter(key => state.toJSON().hasOwnProperty(`contact_${key}`))
					.reduce((acc, key) => {
						acc[`contact_${key}`] = json[key];
						return acc;
					}, {});
				state.set(data);
				state.trigger('change:contact');
				self.unblock();
			});
		},
		onAddItem(e) {
			e.preventDefault();
			var self = this;
			const {state} = this.options;
			const item_id = parseInt(e.params.data.id, 10) || null;
			if (!item_id) {
				return;
			}
			$(e.target).val(null).trigger('change');
			const item = new eac_api.Item({id: item_id});
			self.block();
			item.fetch().then((json) => {
				self.unblock();
				const items = {
					...json,
					quantity: 1,
					item_id: item_id,
					id: _.uniqueId('item_'),
				}

				state.get('items').push(items);
			});
		},
		block: function () {
			// Check if already blocked
			if (this.$el.find('.blockUI').length > 0) return this;

			// Ensure position is relative
			if (this.$el.css('position') === 'static') {
				this.$el.css('position', 'relative');
			}

			// Create overlay
			$('<div class="blockUI"></div>').css({
				position: 'absolute',
				top: 0,
				left: 0,
				width: '100%',
				height: '100%',
				backgroundColor: 'rgb(255, 255, 255)',
				opacity: 0.1,
				cursor: 'wait',
				zIndex: 9999
			}).appendTo(this.$el);

			return this;
		},
		unblock: function () {
			this.$el.find('.blockUI').remove(); // Remove overlay
			return this;
		}
	});

	Bill.Init = function () {
		var state = new Bill.State({
			name: 'John Doe',
		});
		const form = new Bill.Form({state: state});
		//form.render();
	};

	Bill.Init();
});
