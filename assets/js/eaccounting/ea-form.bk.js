jQuery(function ($) {
	$.fn.eaccounting_form = function (options) {
		return this.each(function () {
			new $.eaccounting_form(this, options);
		});
	};

	$.eaccounting_form = function (form, options) {
		this.form = form;
		this.$form = $(form);
		this.options = options;
		var plugin = this;

		//fields
		this.$account_select = $('#account_id, #from_account_id', this.$form);
		this.$currency_select = $('#currency_code', this.$form);
		this.$customer_select = $('#customer_id', this.$form);
		this.$vendor_select = $('#vendor_id', this.$form);
		this.$category_select = $('#category_id', this.$form);
		this.$amount_input = $('#amount, #opening_balance', this.$form);
		this.$currency_code_select = $('#code, #currency_code', this.$form);
		this.$uploader = $('[type="file"]', this.$form);
		this.$file_value = $('.ea-file-input', this.$form);
		this.$file_preview = $('.ea-file', this.$el);

		this.block = function () {
			this.$form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			});
		};

		this.unblock = function () {
			this.$form.unblock();
		};

		this.handle_error = function (error) {
			console.warn(error);
			plugin.unblock();
			$.eaccounting_notice(error, 'error');
		};

		this.mask_input = function (currency) {
			plugin.$amount_input.inputmask('decimal', {
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
		}

		this.get_account = function (account_id, on_success) {
			wp.ajax.send('eaccounting_get_account', {
				data: {
					id: account_id,
					_wpnonce: eaccounting_form_i10n.nonce.get_account,
				},
				success: on_success,
				error: plugin.handle_error,
			});
		}

		this.get_currency = function (code, on_success) {
			console.log('eaccounting_get_currency');
			wp.ajax.send('eaccounting_get_currency', {
				data: {
					code: code,
					_wpnonce: eaccounting_form_i10n.nonce.get_currency,
				},
				success: on_success,
				error: plugin.handle_error,
			});
		}

		/**
		 * When account changed we need to adjust
		 * the amount field to adjust the formatting.
		 */
		this.$account_select.on('change', function () {
			if (!plugin.$amount_input.length) {
				return false;
			}
			var account_id = parseInt(plugin.$account_select.val(), 10);
			if (!account_id) {
				return false;
			}

			plugin.block();

			var update_amount_input = function (code) {
				plugin.get_currency(code, function (res) {
					plugin.mask_input(res);
					plugin.unblock();
				});
			}

			plugin.get_account(account_id, function (res) {
				update_amount_input(res.currency_code);
			});
		});

		this.$currency_select.on('change', function () {
			if (!plugin.$amount_input.length) {
				return false;
			}
			var code = plugin.$currency_select.val();
			if (!code) {
				return false;
			}

			plugin.block();

			plugin.get_currency(code, function (res) {
				plugin.mask_input(res);
				plugin.unblock();
			});

		})

		/**
		 * When currency code changed on currency create
		 * form auto populate currency configs.
		 */
		this.$currency_code_select.on('change', function () {
			var code = plugin.$currency_code_select.val();
			if (!code) {
				return false;
			}
			try {
				var currency = eaccounting_form_i10n.global_currencies[code];
				$('#precision', plugin.$form).val(currency.precision).change();
				$('#position', plugin.$form).val(currency.position).change();
				$('#symbol', plugin.$form).val(currency.symbol).change();
				$('#decimal_separator', plugin.$form)
					.val(currency.decimal_separator)
					.change();
				$('#thousand_separator', plugin.$form)
					.val(currency.thousand_separator)
					.change();
			} catch (e) {
				console.warn(e.message);
			}
		});


		plugin.$uploader.on('change', function (e) {
			var data = new FormData();
			data.append('nonce', plugin.$uploader.data('nonce'));
			data.append('upload', plugin.$uploader[0].files[0]);
			data.append('limit', plugin.$uploader.data('limit'));
			data.append('action', 'eaccounting_upload_files');
			plugin.block();
			window.wp.ajax.send({
				type: 'POST',
				data: data,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success: function (res) {
					plugin.unblock();
					plugin.$file_value.val(res.url);
					plugin.$file_preview.find('.ea-file-link').text(res.name).attr('href', res.url);
					plugin.$file_preview.show();
					plugin.$uploader.hide();
				},
				error: function (error) {
					plugin.handle_error(error);
				},
			});
		});

		plugin.$form.find('.ea-file-delete').on('click', function (e) {
			e.preventDefault();
			plugin.$file_value.val('');
			plugin.$file_preview.find('.ea-file-link').text('').attr('href', '');
			plugin.$file_preview.hide();
			plugin.$uploader.show();
		})


		this.$form.on('submit', function (e) {
			e.preventDefault();
			plugin.block();
			// var checkboxes = $('input:checkbox', plugin.$form).map(function () {
			// 	return {name: this.name, value: this.checked ? this.value : "false"};
			// });

			wp.ajax.send({
				data: plugin.$form.serializeAssoc(),
				success: function (res) {
					plugin.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					plugin.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		});


//
// form.maskAmount = function ( currency ) {
// 	form.amount.inputmask( 'decimal', {
// 		alias: 'numeric',
// 		groupSeparator: currency.thousand_separator,
// 		autoGroup: true,
// 		digits: currency.precision,
// 		radixPoint: currency.decimal_separator,
// 		digitsOptional: false,
// 		allowMinus: false,
// 		prefix: currency.symbol,
// 		placeholder: '0.000',
// 		rightAlign: 0,
// 	} );
// };
//
// form.getCurrency = function ( code, onSuccess, onError ) {
// 	wp.ajax.send( 'eaccounting_get_currency', {
// 		data: {
// 			code: code,
// 			_wpnonce: eaccounting_form_i10n.nonce.get_currency,
// 		},
// 		success: onSuccess,
// 		error: onError,
// 	} );
// };
//
// form.getAccount = function ( id, onSuccess, onError ) {
// 	wp.ajax.send( 'eaccounting_get_account', {
// 		data: {
// 			id: id,
// 			_wpnonce: eaccounting_form_i10n.nonce.get_account,
// 		},
// 		success: onSuccess,
// 		error: onError,
// 	} );
// };
//
// //bind events
// form.$el.on( 'submit', function ( e ) {
// 	e.preventDefault();
// 	form.block();
// 	// var checkboxes = $('input:checkbox', form.$el).map(function() {
// 	// 	return { name: this.name, value: this.checked ? this.value : "false" };
// 	// });
// 	//
// 	wp.ajax.send( {
// 		data: form.$el.serializeObject(),
// 		success: function ( res ) {
// 			form.unblock();
// 			$.eaccounting_notice( res, 'success' );
// 			$.eaccounting_redirect( res );
// 		},
// 		error: function ( error ) {
// 			console.warn( error );
// 			form.unblock();
// 			$.eaccounting_notice( error.message, 'error' );
// 		},
// 	} );
// } );
//
// //on currency change
// form.currency_code.on( 'change', function () {
// 	if ( form.amount.length ) {
// 		var code = form.currency_code.val();
// 		form.block();
// 		form.getCurrency(
// 			code,
// 			function ( res ) {
// 				form.maskAmount( res );
// 				form.unblock();
// 			},
// 			form.onError
// 		);
// 	}
// } );
//
// //on account change
// form.account_id.on( 'change', function () {
// 	if ( form.amount.length ) {
// 		var account_id = form.account_id.val();
// 		var id = parseInt( account_id, 10 );
// 		if ( ! id ) {
// 			return;
// 		}
// 		form.block();
// 		form.getAccount(
// 			id,
// 			function ( res ) {
// 				form.getCurrency(
// 					res.currency_code,
// 					function ( code ) {
// 						form.maskAmount( code );
// 						form.unblock();
// 					},
// 					form.onError
// 				);
// 			},
// 			form.onError
// 		);
// 	}
// } );
//
// //on code change
// form.code.on( 'change', function () {
// 	var code = form.code.val();
// 	console.log( code );
// 	if ( ! code ) {
// 		return false;
// 	}
// 	try {
// 		currency = eaccounting_form_i10n.global_currencies[ code ];
// 		$( '#precision', form.$el ).val( currency.precision ).change();
// 		$( '#position', form.$el ).val( currency.position ).change();
// 		$( '#symbol', form.$el ).val( currency.symbol ).change();
// 		$( '#decimal_separator', form.$el )
// 			.val( currency.decimal_separator )
// 			.change();
// 		$( '#thousand_separator', form.$el )
// 			.val( currency.thousand_separator )
// 			.change();
// 	} catch ( e ) {
// 		console.warn( e.message );
// 	}
// } );
//
// form.uploader.on( 'change', function ( e ) {
// 	var data = new FormData();
// 	data.append( 'nonce', $( this ).data( 'nonce' ) );
// 	data.append( 'upload', $( this )[ 0 ].files[ 0 ] );
// 	data.append( 'limit', $( this ).data( 'limit' ) );
// 	data.append( 'action', 'eaccounting_upload_files' );
// 	form.block();
// 	window.wp.ajax.send( {
// 		type: 'POST',
// 		data: data,
// 		dataType: 'json',
// 		cache: false,
// 		contentType: false,
// 		processData: false,
// 		success: function ( res ) {
// 			var item = $( '<li>' ).append(
// 				'<a href="' +
// 				res.url +
// 				'" target="_blank">' +
// 				res.name +
// 				'</a>'
// 			);
// 			item.append(
// 				'<a href="#" class="delete"><span class="dashicons dashicons-no-alt"></span></a>'
// 			);
// 			form.files.append( item );
// 			form.unblock();
// 		},
// 		error: function ( error ) {
// 			form.unblock();
// 		},
// 	} );
// } );
//


		this.init = function () {
			//trigger change so others input change adjust.
			if (plugin.$account_select.length)
				plugin.$account_select.trigger('change');
			if (plugin.$currency_select.length)
				plugin.$currency_select.trigger('change');
		}

		this.init();
		return this;
	};

	$(document).ready(function () {
		$('.ea-ajax-form').eaccounting_form();
	});

	//Common

	// /**
	//  * Get account object by account_id.
	//  *
	//  * @param account_id
	//  * @param onSuccess
	//  * @param OnError
	//  * @returns {boolean}
	//  */
	// var eaccounting_get_account = function (account_id, onSuccess, OnError) {
	// 	if (!account_id) {
	// 		return false;
	// 	}
	//
	// 	wp.ajax.send('eaccounting_get_account', {
	// 		data: {
	// 			id: account_id,
	// 			_wpnonce: eaccounting_form_i10n.nonce.get_account,
	// 		},
	// 		success: onSuccess,
	// 		error: OnError,
	// 	});
	// }
	// /**
	//  * Get account object by account_id.
	//  *
	//  * @param account_id
	//  * @param onSuccess
	//  * @param OnError
	//  * @returns {boolean}
	//  */
	// var eaccounting_get_account_currency = function (account_id, onSuccess, OnError) {
	// 	if (!account_id) {
	// 		return false;
	// 	}
	//
	// 	wp.ajax.send('eaccounting_get_account_currency', {
	// 		data: {
	// 			id: account_id,
	// 			_wpnonce: eaccounting_form_i10n.nonce.get_currency,
	// 		},
	// 		success: onSuccess,
	// 		error: OnError,
	// 	});
	// }
	//
	// /**
	//  * Get currency object by code.
	//  *
	//  * @param code
	//  * @param onSuccess
	//  * @param OnError
	//  * @returns {boolean}
	//  */
	// var eaccounting_get_currency = function (code, onSuccess, OnError) {
	// 	if (!code) {
	// 		return false;
	// 	}
	// 	wp.ajax.send('eaccounting_get_currency', {
	// 		data: {
	// 			code: code,
	// 			_wpnonce: eaccounting_form_i10n.nonce.get_currency,
	// 		},
	// 		success: onSuccess,
	// 		error: OnError,
	// 	});
	// }


	/**
	 * Mask currency input field
	 * @param $el
	 * @param currency
	 */
	var eaccounting_mask_input = function ($el, currency) {
		$el.inputmask('decimal', {
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
	}

	// Tax form
	var eaccounting_tax_form = {
		init: function () {
			$('#ea-tax-form')
				.on('change keyup', 'input[name="rate"]', this.input_tax_rate)
				.on('submit', this.submit);

			$(document).on('ready', function () {
				$('#ea-tax-form input[name="rate"]').trigger('change');
			});
		},
		block: function () {
			$('#ea-tax-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-tax-form').unblock();
		},
		input_tax_rate: function (e) {
			e.target.value = e.target.value.replace(/[^0-9.]/g, '');
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_tax_form.block();
			wp.ajax.send({
				data: $('#ea-tax-form').serializeAssoc(),
				success: function (res) {
					eaccounting_tax_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_tax_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	// Revenue Form
	var eaccounting_revenue_form = {
		init: function () {
			$('#ea-revenue-form')
				.on('change', '#account_id', this.update_amount_input)
				.on('submit', this.submit);
			$(document).on('ready', function () {
				$('#ea-revenue-form #account_id').trigger('change');
			});
		},
		block: function () {
			$('#ea-revenue-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-revenue-form').unblock();
		},
		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			if (!account_id) {
				return false;
			}
			eaccounting_revenue_form.block();
			var $currency_input = $('#ea-revenue-form #amount');
			wp.ajax.send('eaccounting_get_account_currency', {
				data: {
					account_id: account_id,
					_wpnonce: eaccounting_form_i10n.nonce.get_currency,
				},
				success: function (res) {
					eaccounting_mask_input($currency_input, res);
					eaccounting_revenue_form.unblock();
				},
				error: function (error) {
					console.warn(error);
					eaccounting_revenue_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				}
			});
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_revenue_form.block();
			wp.ajax.send({
				data: $('#ea-tax-form').serializeAssoc(),
				success: function (res) {
					eaccounting_revenue_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_revenue_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	//category form
	var eaccounting_category_form = {
		init: function () {
			$('#ea-category-form').on('submit', this.submit);
		},
		block: function () {
			$('#ea-category-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-category-form').unblock();
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_category_form.block();
			wp.ajax.send({
				data: $('#ea-category-form').serializeAssoc(),
				success: function (res) {
					eaccounting_category_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_category_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	//currency form
	var eaccounting_currency_form = {
		init: function () {
			$('#ea-currency-form')
				.on('change', '#code', this.update_currency_fields)
				.on('submit', this.submit);
		},
		block: function () {
			$('#ea-currency-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-currency-form').unblock();
		},
		update_currency_fields: function (e) {
			var code = e.target.value;
			if (!code) {
				return false;
			}
			var currency = eaccounting_form_i10n.global_currencies[code];
			$('#name', '#ea-currency-form').val(currency.name).change()
			$('#precision', '#ea-currency-form').val(currency.precision).change();
			$('#position', '#ea-currency-form').val(currency.position).change();
			$('#symbol', '#ea-currency-form').val(currency.symbol).change();
			$('#decimal_separator', '#ea-currency-form')
				.val(currency.decimal_separator)
				.change();
			$('#thousand_separator', '#ea-currency-form')
				.val(currency.thousand_separator)
				.change();
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_currency_form.block();
			wp.ajax.send({
				data: $('#ea-currency-form').serializeAssoc(),
				success: function (res) {
					eaccounting_currency_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_currency_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	//item form
	var eaccounting_item_form = {
		init: function () {
			$('#ea-item-form')
				.on('change keyup', 'input[name="quantity"]', this.input_item_quantity)
				.on('submit', this.submit);
		},
		block: function () {
			$('#ea-item-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-item-form').unblock();
		},
		input_item_quantity: function (e) {
			e.target.value = e.target.value.replace(/[^0-9.]/g, '');
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_item_form.block();
			wp.ajax.send({
				data: $('#ea-item-form').serializeObject(),
				success: function (res) {
					eaccounting_item_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_item_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	//account form
	var eaccounting_account_form = {
		init: function () {
			$("#ea-account-form")
				.on('change', '#currency_code', this.update_opening_balance)
				.on('submit', this.submit);

			$(document).on('ready', function () {
				$('#ea-account-form #currency_code').trigger('change');
			});
		},
		block: function () {
			$('#ea-account-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-account-form').unblock();
		},
		update_opening_balance: function (e) {
			var code = e.target.value;
			if (!code) {
				return false;
			}
			eaccounting_account_form.block();
			var $opening_balance = $("#ea-account-form #opening_balance");
			wp.ajax.send('eaccounting_get_currency', {
				data: {
					code: code,
					_wpnonce: eaccounting_form_i10n.nonce.get_currency,
				},
				success: function (res) {
					eaccounting_mask_input($opening_balance, res);
					eaccounting_account_form.unblock();
				},
				error: function (error) {
					console.warn(error);
					eaccounting_account_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				}
			});
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_account_form.block();
			wp.ajax.send({
				data: $('#ea-account-form').serializeAssoc(),
				success: function (res) {
					eaccounting_account_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_account_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}
	//transfer form
	var eaccounting_transfer_form = {
		init: function () {
			$('#ea-transfer-form')
				.on('change', '#from_account_id', this.update_amount_input)
				.on('submit', this.submit);
			$(document).on('ready', function () {
				$('#ea-transfer-form #from_account_id').trigger('change');
			});
		},
		block: function () {
			$('#ea-transfer-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-transfer-form').unblock();
		},
		update_amount_input: function (e) {
			var account_id = parseInt(e.target.value, 10);
			if (!account_id) {
				return false;
			}
			eaccounting_transfer_form.block();
			var $currency_input = $('#ea-transfer-form #amount');
			wp.ajax.send('eaccounting_get_account_currency', {
				data: {
					account_id: account_id,
					_wpnonce: eaccounting_form_i10n.nonce.get_currency,
				},
				success: function (res) {
					eaccounting_mask_input($currency_input, res);
					eaccounting_transfer_form.unblock();
				},
				error: function (error) {
					console.warn(error);
					eaccounting_transfer_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				}
			});
		},
		submit: function (e) {
			e.preventDefault();
			eaccounting_transfer_form.block();
			wp.ajax.send({
				data: $('#ea-transfer-form').serializeAssoc(),
				success: function (res) {
					eaccounting_transfer_form.unblock();
					$.eaccounting_notice(res, 'success');
					$.eaccounting_redirect(res);
				},
				error: function (error) {
					console.warn(error);
					eaccounting_transfer_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		}
	}

	var eaccounting_invoice_form = {
		$form : $('#ea-invoice-form'),
		init: function () {
			console.log(this);
			// $(document).on('change keyup', '.ea-invoice-line-price input', this.number_input);
			// $(document).on('change keyup', '.ea-invoice-line-tax input', this.number_input);
			// $(document).on('change keyup', '.ea-invoice-line-quantity input', this.number_input);
			// $(document).on('change keyup', '.ea-invoice-line-vat input', this.number_input);
			// $(document).on('click', '#ea-invoice-form .save-line, #ea-invoice-form .edit-line', this.toggle_line_edit);
			// $(document).on('click', '#ea-invoice-form .delete-line', this.remove_line);
			// $(document).on('click', '#ea-invoice-form .ea-add-line-item', this.add_line);
			// $(document).on('click', '#ea-invoice-form .recalculate-totals', this.recalculate_totals);
			// $(document).on('submit', '#ea-invoice-form', this.submit);
			// $('body').on('ea_invoice_updated', this.calculate_total);
			// $('.ea-invoice-line-name input[type="text"]').autocomplete(this.items_search());
		},
		block: function () {
			$('#ea-invoice-form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},
		unblock: function () {
			$('#ea-invoice-form').unblock();
		},
		items_search: function () {

			return {
				selectFirst: true,
				source: function (params, suggest) {
					wp.ajax.send({
						data: {
							search: params.term,
							action: 'eaccounting_get_items'
						},
						success: function (items) {
							suggest($.map(items, function (item) {
								return {
									value: item.name + ' ' + '(' + item.sku + ')',
									data: item,
									id: item.id
								};
							}));
						},
					});
				},
				classes: {
					"ui-autocomplete": "your-custom-class",
				},
				autoFocus: true,
				select: function (event, ui) {
					var item = ui.item;
					console.log(this);
				}
			}
		},
		number_input: function (e) {
			e.target.value = e.target.value.replace(/[^0-9.]/g, '');
		},
		toggle_line_edit: function (e) {
			e.preventDefault();
			var $tr = $(this).closest('.ea-invoice-line');
			if ($tr.hasClass('editing')) {
				$tr.removeClass('editing');
				$('body').trigger('ea_invoice_updated');
				return false;
			}
			$tr.siblings('tr').removeClass('editing');
			$tr.addClass('editing');

		},
		remove_line: function (e) {
			e.preventDefault();
			$(this).closest('.ea-invoice-line').remove();
			$('body').trigger('ea_invoice_updated');
		},
		add_line: function (e) {
			e.preventDefault();
			console.log(this);
			// var index = parseInt($('#ea-invoice-form .ea-invoice-line').length, 10) + 1;
			// $("<input>").attr("type", "hidden").attr('name', 'line_items[' + index + '][item_id]').attr('value', 6).appendTo("#ea-invoice-form .ea-invoice-line-items");
			// $("<input>").attr("type", "hidden").attr('name', 'line_items[' + index + '][quantity]').attr('value', 10).appendTo("#ea-invoice-form .ea-invoice-line-items");
			// $('body').trigger('ea_invoice_updated');
		},
		disable_fields: function () {

		},
		recalculate_totals: function (e) {
			e.preventDefault();
			eaccounting_invoice_form.calculate_total();
		},
		calculate_total: function () {
			eaccounting_invoice_form.block();
			var data = $('#ea-invoice-form').serializeObject();
			data.action = 'eaccounting_invoice_calculate_totals'
			wp.ajax.send({
				data: data,
				success: function (res) {
					$('#ea-invoice-form .ea-invoice-line-items').html(res.lines_html);
					$('#ea-invoice-form .ea-invoice-totals').html(res.totals_html);
					eaccounting_invoice_form.unblock();
				},
				error: function (error) {
					console.warn(error);
					eaccounting_invoice_form.unblock();
					$.eaccounting_notice(error.message, 'error');
				},
			});
		},
		submit: function (e) {
			e.preventDefault();
			console.log($('#ea-invoice-form').serializeObject())
		}
	}


	eaccounting_invoice_form.init();
	eaccounting_tax_form.init();
	eaccounting_revenue_form.init();

	// $.fn.ea_form = function (callback) {
	// 	return this.each(function () {
	// 		new ea_form(this, callback);
	// 	});
	// };
	//
	// var ea_form = function (form, callback) {
	// 	this.el = form;
	// 	this.$el = $(form);
	// 	this.rules = {};
	// 	this.data = this.$el.serializeObject();
	// 	var plugin = this;
	//
	// 	/**
	// 	 * Get a form input value.
	// 	 *
	// 	 * @param {string} name
	// 	 * @returns {boolean|*}
	// 	 */
	// 	this.get = function (name) {
	// 		if (plugin.data.hasOwnProperty(name)) {
	// 			return plugin.data[name];
	// 		}
	// 		return false;
	// 	}
	//
	// 	/**
	// 	 * Block the form when need to validate or make ajax call.
	// 	 */
	// 	this.block = function () {
	// 		plugin.$el.block({
	// 			message: null,
	// 			overlayCSS: {
	// 				background: '#fff',
	// 				opacity: 0.6
	// 			}
	// 		});
	// 	}
	//
	// 	/**
	// 	 * Unblock the form when checking is done.
	// 	 */
	// 	this.unblock = function () {
	// 		plugin.$el.unblock();
	// 	}
	//
	// 	/**
	// 	 * Fire action hook
	// 	 * @param action
	// 	 * @returns {function(): void}
	// 	 */
	// 	this.do_action = function (action) {
	// 		return function () {
	// 			plugin.$el.trigger(action);
	// 		}
	// 	}
	//
	// 	/**
	// 	 * Apply an action hook.
	// 	 *
	// 	 * @param action
	// 	 * @param callback
	// 	 */
	// 	this.add_action = function (action, callback) {
	// 		plugin.$el.on(action, function () {
	// 			callback(plugin)
	// 		});
	// 	}
	//
	// 	this.submit = function (e){
	// 		e.preventDefault();
	// 		console.log(plugin.data);
	// 	}
	//
	// 	this.$el.on('submit', plugin.submit);
	//
	//
	// 	if ('function' === typeof callback) {
	// 		callback(this);
	// 	}
	// }
	//
	// $('#ea-invoice-form').ea_form(function (form) {
	// 	/**
	// 	 * Calculate total
	// 	 */
	// 	form.calculate_total = function () {
	// 		console.log('calculate_total');
	// 		form.block();
	// 		wp.ajax.send({
	// 			data: $.extend({}, form.data, {action: 'eaccounting_invoice_calculate_totals'}),
	// 			success: function (res) {
	// 				$('#ea-invoice-form .ea-invoice-line-items').html(res.lines_html);
	// 				$('#ea-invoice-form .ea-invoice-totals').html(res.totals_html);
	// 				form.unblock();
	// 			},
	// 			error: function (error) {
	// 				console.warn(error);
	// 				form.unblock();
	// 				$.eaccounting_notice(error.message, 'error');
	// 			},
	// 		});
	// 	}
	//
	// 	$('.ea-add-line-item', form.$el).on('click', function (){
	// 		$( form.$el ).ea_modal( {
	// 			template: 'ea-modal-add-invoice-item',
	// 			onSubmit: function (){
	//
	// 			},
	// 			onReady: function ( el, $modal ) {
	//
	// 			},
	// 		} );
	// 	});
	//
	// 	$('.save-line, .edit-line', form.$el).on('click',function (e){
	// 		e.preventDefault();
	// 		var $tr = $(this).closest('.ea-invoice-line');
	// 		console.log($tr);
	// 		if ($tr.hasClass('editing')) {
	// 			$tr.removeClass('editing');
	// 			form.do_action('ea_invoice_updated');
	// 		}else{
	// 			$tr.siblings('tr').removeClass('editing');
	// 			$tr.addClass('editing');
	// 		}
	// 	});
	//
	// 	$('.recalculate-totals', form.$el).on('click', form.do_action('ea_invoice_updated'));
	// 	form.add_action('ea_invoice_updated', form.calculate_total);
	// });


});