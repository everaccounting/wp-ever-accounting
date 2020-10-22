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

});
