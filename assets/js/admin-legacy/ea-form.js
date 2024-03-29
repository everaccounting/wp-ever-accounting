jQuery( function ( $ ) {
	'use strict';

	const maskInput = function ( el, json ) {
		$( el ).inputmask( 'decimal', {
			alias: 'numeric',
			groupSeparator: json.thousand_separator,
			autoGroup: true,
			digits: json.precision,
			radixPoint: json.decimal_separator,
			digitsOptional: false,
			allowMinus: false,
			prefix: json.symbol,
			placeholder: '0.000',
			rightAlign: 0,
			autoUnmask: true,
		} );
	};

	var revenue_form = {
		init() {
			$( document )
				.on(
					'change',
					'#ea-revenue-form [name="account_id"]',
					this.update_amount_input
				)
				.on( 'submit', '#ea-revenue-form', this.submit );
		},

		block() {
			$( '#ea-revenue-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-revenue-form' ).unblock();
		},

		update_amount_input( e ) {
			const account_id = parseInt( e.target.value, 10 );
			if ( isNaN( account_id ) ) {
				return false;
			}
			revenue_form.block();
			const data = {
				action: 'eaccounting_get_account_currency',
				account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			};
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					maskInput(
						$( '#ea-revenue-form [name="amount"]' ),
						json.data
					);
				}
			} ).always( function ( json ) {
				revenue_form.unblock();
				$.eaccounting_notice( json );
			} );
		},

		submit( e ) {
			e.preventDefault();
			revenue_form.block();
			const data = $( '#ea-revenue-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				revenue_form.unblock();
			} );
		},
	};

	var payment_form = {
		init() {
			$( document )
				.on(
					'change',
					'#ea-payment-form [name="account_id"]',
					this.update_amount_input
				)
				.on( 'submit', '#ea-payment-form', this.submit );
		},

		block() {
			$( '#ea-payment-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-payment-form' ).unblock();
		},

		update_amount_input( e ) {
			const account_id = parseInt( e.target.value, 10 );
			if ( isNaN( account_id ) ) {
				return false;
			}
			payment_form.block();
			const data = {
				action: 'eaccounting_get_account_currency',
				account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			};
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					maskInput(
						$( '#ea-payment-form [name="amount"]' ),
						json.data
					);
				}
			} ).always( function ( json ) {
				payment_form.unblock();
				$.eaccounting_notice( json );
			} );
		},

		submit( e ) {
			e.preventDefault();
			payment_form.block();
			const data = $( '#ea-payment-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				payment_form.unblock();
			} );
		},
	};

	var account_form = {
		init() {
			$( '#ea-account-form' )
				.on(
					'select2:select',
					'[name="currency_code"]',
					this.update_amount_input
				)
				.on( 'submit', this.submit );
		},

		block() {
			$( '#ea-account-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-account-form' ).unblock();
		},

		update_amount_input( e ) {
			const data = e.params.data;
			const code = data.id;
			if ( ! code ) {
				return false;
			}
			const currency = eaccounting_form_i10n.global_currencies[ code ];
			maskInput(
				$( '#ea-account-form [name="opening_balance"]' ),
				currency
			);
		},

		submit( e ) {
			e.preventDefault();
			account_form.block();
			const data = $( '#ea-account-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				account_form.unblock();
			} );
		},
	};

	var transfer_form = {
		init() {
			$( '#ea-transfer-form' )
				.on(
					'change',
					'[name="from_account_id"]',
					this.update_amount_input
				)
				.find( '#from_account_id' )
				.trigger( 'change' )
				.end()
				.on( 'submit', this.submit );
		},

		block() {
			$( '#ea-customer-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-transfer-form' ).unblock();
		},

		update_amount_input( e ) {
			const account_id = parseInt( e.target.value, 10 );
			if ( isNaN( account_id ) ) {
				return false;
			}
			transfer_form.block();
			const data = {
				action: 'eaccounting_get_account_currency',
				account_id,
				_wpnonce: eaccounting_form_i10n.nonce.get_currency,
			};
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					maskInput(
						$( '#ea-transfer-form [name="amount"]' ),
						json.data
					);
				}
			} ).always( function ( json ) {
				transfer_form.unblock();
				$.eaccounting_notice( json );
			} );
		},

		submit( e ) {
			e.preventDefault();
			transfer_form.block();
			const data = $( '#ea-transfer-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				transfer_form.unblock();
			} );
		},
	};

	var customer_form = {
		init() {
			$( '#ea-customer-form' ).on( 'submit', this.submit );
		},
		block() {
			$( '#ea-customer-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-customer-form' ).unblock();
		},
		submit( e ) {
			e.preventDefault();
			customer_form.block();
			const data = $( '#ea-customer-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				customer_form.unblock();
			} );
		},
	};

	var vendor_form = {
		init() {
			$( '#ea-vendor-form' ).on( 'submit', this.submit );
		},
		block() {
			$( '#ea-vendor-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-vendor-form' ).unblock();
		},
		submit( e ) {
			e.preventDefault();
			vendor_form.block();
			const data = $( '#ea-vendor-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				vendor_form.unblock();
			} );
		},
	};

	var category_form = {
		init() {
			$( '#ea-category-form' ).on( 'submit', this.submit );
		},
		block() {
			$( '#ea-category-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-category-form' ).unblock();
		},
		submit( e ) {
			e.preventDefault();
			category_form.block();
			const data = $( '#ea-category-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				category_form.unblock();
			} );
		},
	};

	const item_form = {
		init() {
			$( '#ea-item-form' ).on( 'submit', this.submit );
		},
		block() {
			$( '#ea-item-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-item-form' ).unblock();
		},
		submit( e ) {
			e.preventDefault();
			category_form.block();
			const data = $( '#ea-item-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				category_form.unblock();
			} );
		},
	};

	var currency_form = {
		init() {
			$( '#ea-currency-form' )
				.on( 'change', '[name="code"]', this.update_currency_props )
				.on( 'submit', this.submit );
		},
		block() {
			$( '#ea-currency-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-currency-form' ).unblock();
		},

		update_currency_props( e ) {
			const code = e.target.value;
			if ( ! code ) {
				return false;
			}
			const currency = eaccounting_form_i10n.global_currencies[ code ];
			$( '[name="name"]', '#ea-currency-form' )
				.val( currency.name )
				.change();
			$( '[name="precision"]', '#ea-currency-form' )
				.val( currency.precision )
				.change();
			$( '[name="position"]', '#ea-currency-form' )
				.val( currency.position )
				.change();
			$( '[name="symbol"]', '#ea-currency-form' )
				.val( currency.symbol )
				.change();
			$( '[name="decimal_separator"]', '#ea-currency-form' )
				.val( currency.decimal_separator )
				.change();
			$( '[name="thousand_separator"]', '#ea-currency-form' )
				.val( currency.thousand_separator )
				.change();
		},

		submit( e ) {
			e.preventDefault();
			currency_form.block();
			const data = $( '#ea-currency-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				currency_form.unblock();
			} );
		},
	};

	var invoice_form = {
		init() {
			$( '#ea-invoice-form' )
				.on( 'click', '.add-line-item', this.add_line_item )
				.on( 'select2:select', '.select-item', this.item_selected )
				.on( 'click', '.delete-line', this.remove_line_item )
				.on( 'click', '.edit-line, .save-line', this.edit_line_item )
				.on( 'click', '.add-discount', this.add_discount )
				.on( 'change', '[name="currency_code"]', this.recalculate )
				.on( 'click', '.recalculate', this.recalculate )
				.on( 'submit', this.submit );

			$( '#ea-invoice' )
				.on( 'click', '.delete_note', this.delete_note )
				.on( 'click', '.receive-payment', this.receive_payment );

			$( document ).on( 'submit', '#invoice-note-form', this.add_note );

			$( document.body ).on( 'ea_invoice_updated', this.recalculate );
		},

		block() {
			$( '#ea-invoice-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-invoice-form' ).unblock();
		},

		add_line_item( e ) {
			e.preventDefault();
			const line_item = $( $( '#ea-invoice-line-template' ).html() );
			const item_selector = $( $( '#ea-invoice-item-selector' ).html() );
			let item_selector_name = item_selector.attr( 'name' );
			//var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
			const index = Array( 1 )
				.fill( null )
				.map( function () {
					return Math.random().toString( 10 ).substr( 2 );
				} )
				.join( '' );
			line_item.addClass( 'editing' );
			$( line_item )
				.find( ':input' )
				.each( function () {
					let name = $( this ).attr( 'name' );
					name = name.replace( /\[(\d+)\]/, '[' + index + ']' );
					$( this ).attr( 'name', name ).attr( 'id', name );
				} );
			item_selector_name = item_selector_name.replace(
				/\[(\d+)\]/,
				'[' + index + ']'
			);
			item_selector
				.attr( 'name', item_selector_name )
				.attr( 'id', item_selector_name );
			item_selector.css( { width: '90%', maxWidth: 'none' } );
			line_item.find( '.ea-document__line-name' ).html( item_selector );
			$( '#ea-document__line-items' ).append( line_item );
			$( document.body ).trigger( 'ea_select2_init' );
		},

		item_selected( e ) {
			const data = e.params.data;
			$( e.target )
				.closest( 'tr' )
				.find( 'input.line_item_price' )
				.val( data.item.sale_price )
				.end()
				.find( 'input.line_item_quantity' )
				.val( 1 )
				.end()
				.find( 'input.line_item_tax' )
				.val( data.item.sales_tax );
			$( 'body' ).trigger( 'ea_invoice_updated' );
		},

		remove_line_item( e ) {
			e.preventDefault();
			$( this ).closest( 'tr' ).remove();
			invoice_form.recalculate();
		},

		edit_line_item( e ) {
			e.preventDefault();
			const $tr = $( this ).closest( '.ea-document__line' );
			if ( $tr.hasClass( 'editing' ) ) {
				$tr.removeClass( 'editing' );
				$( 'body' ).trigger( 'ea_invoice_updated' );
				return false;
			}
			$tr.siblings( 'tr' ).removeClass( 'editing' );
			$tr.addClass( 'editing' );
		},

		delete_note( e ) {
			e.preventDefault();
			const note = $( this ).closest( '.ea-document-notes__item' );
			const nonce = note.data( 'nonce' );
			const note_id = note.data( 'noteid' );

			const data = {
				action: 'eaccounting_delete_note',
				id: note_id,
				nonce,
			};
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					note.remove();
					$( '#ea-invoice_notes-body' ).replaceWith(
						json.data.notes
					);
				}
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
			} );
		},

		add_discount( e ) {
			$( '#ea-modal-add-discount' ).ea_modal( {
				onReady( plugin ) {
					$( '#discount', plugin.$modal ).val(
						$( '#ea-invoice-form #discount' ).val()
					);
					$( '#discount_type', plugin.$modal ).val(
						$( '#ea-invoice-form #discount_type' ).val()
					);
				},
				onSubmit( data, modal ) {
					$( '#ea-invoice-form #discount' ).val( data.discount );
					$( '#ea-invoice-form #discount_type' ).val(
						data.discount_type
					);
					modal.close();
					invoice_form.recalculate();
				},
			} );
		},

		receive_payment( e ) {
			e.preventDefault();
			const $modal_selector = $( '#ea-modal-add-invoice-payment' );
			const code = $( this ).data( 'currency' );

			$modal_selector.ea_modal( {
				onReady( plugin ) {
					eaccounting_mask_amount(
						$( '[name="amount"]', plugin.$modal ),
						code
					);
				},
				onSubmit( data, plugin ) {
					$.post( ajaxurl, data, function ( json ) {} ).always(
						function ( json ) {
							plugin.close();
							$.eaccounting_notice( json );
							location.reload();
						}
					);
				},
			} );
		},

		add_note( e ) {
			e.preventDefault();
			const $form = $( this );
			eaccounting_block( $form );
			const data = $form.serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					$( '#ea-invoice_notes-body' ).replaceWith(
						json.data.notes
					);
				}
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				eaccounting_unblock( $form );
				$( '#invoice-note-form textarea' ).val( '' );
			} );
		},

		recalculate() {
			invoice_form.block();
			const data = $.extend(
				{},
				$( '#ea-invoice-form' ).serializeObject(),
				{ action: 'eaccounting_invoice_recalculate' }
			);
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					$(
						'#ea-invoice-form .ea-document__items-wrapper'
					).replaceWith( json.data.html );
				} else {
					$.eaccounting_notice( json );
				}
			} ).always( function ( json ) {
				invoice_form.unblock();
			} );
		},

		submit( e ) {
			e.preventDefault();
			invoice_form.block();
			const data = $( '#ea-invoice-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				invoice_form.unblock();
			} );
		},
	};

	var bill_form = {
		init() {
			$( '#ea-bill-form' )
				.on( 'click', '.add-line-item', this.add_line_item )
				.on( 'select2:select', '.select-item', this.item_selected )
				.on( 'click', '.delete-line', this.remove_line_item )
				.on( 'click', '.edit-line, .save-line', this.edit_line_item )
				.on( 'click', '.add-discount', this.add_discount )
				.on( 'change', '[name="currency_code"]', this.recalculate )
				.on( 'click', '.recalculate', this.recalculate )
				.on( 'submit', this.submit );

			$( '#ea-bill' )
				.on( 'click', '.delete_note', this.delete_note )
				.on( 'click', '.add-payment', this.add_payment );

			$( document ).on( 'submit', '#bill-note-form', this.add_note );

			$( document.body ).on( 'ea_bill_updated', this.recalculate );
		},
		block() {
			$( '#ea-bill-form' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		},

		unblock() {
			$( '#ea-bill-form' ).unblock();
		},
		add_line_item( e ) {
			e.preventDefault();
			const line_item = $( $( '#ea-bill-line-template' ).html() );
			const item_selector = $( $( '#ea-bill-item-selector' ).html() );
			let item_selector_name = item_selector.attr( 'name' );
			//var index = Array(1).fill(null).map(() => Math.random().toString(10).substr(2)).join('');
			const index = Array( 1 )
				.fill( null )
				.map( function () {
					return Math.random().toString( 10 ).substr( 2 );
				} )
				.join( '' );
			line_item.addClass( 'editing' );
			$( line_item )
				.find( ':input' )
				.each( function () {
					let name = $( this ).attr( 'name' );
					name = name.replace( /\[(\d+)\]/, '[' + index + ']' );
					$( this ).attr( 'name', name ).attr( 'id', name );
				} );
			item_selector_name = item_selector_name.replace(
				/\[(\d+)\]/,
				'[' + index + ']'
			);
			item_selector
				.attr( 'name', item_selector_name )
				.attr( 'id', item_selector_name );
			item_selector.css( { width: '90%', maxWidth: 'none' } );
			line_item.find( '.ea-document__line-name' ).html( item_selector );
			$( '#ea-document__line-items' ).append( line_item );
			$( document.body ).trigger( 'ea_select2_init' );
		},

		item_selected( e ) {
			const data = e.params.data;
			$( e.target )
				.closest( 'tr' )
				.find( 'input.line_item_price' )
				.val( data.item.purchase_price )
				.end()
				.find( 'input.line_item_quantity' )
				.val( 1 )
				.end()
				.find( 'input.line_item_tax' )
				.val( data.item.purchase_tax );
			$( 'body' ).trigger( 'ea_bill_updated' );
		},

		remove_line_item( e ) {
			e.preventDefault();
			$( this ).closest( 'tr' ).remove();
			bill_form.recalculate();
		},

		edit_line_item( e ) {
			e.preventDefault();
			const $tr = $( this ).closest( '.ea-document__line' );
			if ( $tr.hasClass( 'editing' ) ) {
				$tr.removeClass( 'editing' );
				$( 'body' ).trigger( 'ea_bill_updated' );
				return false;
			}
			$tr.siblings( 'tr' ).removeClass( 'editing' );
			$tr.addClass( 'editing' );
		},

		delete_note( e ) {
			e.preventDefault();
			const note = $( this ).closest( '.ea-document-notes__item' );
			const nonce = note.data( 'nonce' );
			const note_id = note.data( 'noteid' );

			const data = {
				action: 'eaccounting_delete_note',
				id: note_id,
				nonce,
			};
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					note.remove();
					$( '#ea-bill_notes-body' ).replaceWith( json.data.notes );
				}
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
			} );
		},

		add_discount( e ) {
			$( '#ea-modal-add-discount' ).ea_modal( {
				onReady( plugin ) {
					$( '#discount', plugin.$modal ).val(
						$( '#ea-bill-form #discount' ).val()
					);
					$( '#discount_type', plugin.$modal ).val(
						$( '#ea-bill-form #discount_type' ).val()
					);
				},
				onSubmit( data, modal ) {
					$( '#ea-bill-form #discount' ).val( data.discount );
					$( '#ea-bill-form #discount_type' ).val(
						data.discount_type
					);
					modal.close();
					bill_form.recalculate();
				},
			} );
		},

		add_payment( e ) {
			e.preventDefault();
			const $modal_selector = $( '#ea-modal-add-bill-payment' );
			const code = $( this ).data( 'currency' );

			$modal_selector.ea_modal( {
				onReady( plugin ) {
					eaccounting_mask_amount(
						$( '#amount', plugin.$modal ),
						code
					);
				},
				onSubmit( data, plugin ) {
					$.post( ajaxurl, data, function ( json ) {} ).always(
						function ( json ) {
							plugin.close();
							$.eaccounting_notice( json );
							location.reload();
						}
					);
				},
			} );
		},

		add_note( e ) {
			e.preventDefault();
			const $form = $( this );
			eaccounting_block( $form );
			const data = $form.serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					$( '#ea-bill_notes-body' ).replaceWith( json.data.notes );
				}
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				eaccounting_unblock( $form );
				$( '#bill-note-form textarea' ).val( '' );
			} );
		},

		recalculate() {
			bill_form.block();
			const data = $.extend( {}, $( '#ea-bill-form' ).serializeObject(), {
				action: 'eaccounting_bill_recalculate',
			} );
			$.post( ajaxurl, data, function ( json ) {
				if ( json.success ) {
					$(
						'#ea-bill-form .ea-document__items-wrapper'
					).replaceWith( json.data.html );
				} else {
					$.eaccounting_notice( json );
				}
			} ).always( function ( json ) {
				bill_form.unblock();
			} );
		},

		submit( e ) {
			e.preventDefault();
			bill_form.block();
			const data = $( '#ea-bill-form' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {
				$.eaccounting_redirect( json );
			} ).always( function ( json ) {
				$.eaccounting_notice( json );
				bill_form.unblock();
			} );
		},
	};

	revenue_form.init();
	payment_form.init();
	account_form.init();
	transfer_form.init();
	customer_form.init();
	vendor_form.init();
	category_form.init();
	currency_form.init();
	item_form.init();
	invoice_form.init();
	bill_form.init();
} );
