jQuery( document ).ready( ( $ ) => {
	'use strict';
	$( '#eac-edit-bill' ).eac_form( {
		events: {
			'change :input[name="contact_id"]': 'onChangeContact',
			'change :input[name="currency"]': 'onChangeCurrency',
			'select2:select .add-item': 'onAddItem',
			'change .item-price, .item-quantity': 'onChangeItem',
			'select2:select .item-taxes': 'onAddTax',
			'select2:unselect .item-taxes': 'onRemoveTax',
		},

		onChangeContact( e ) {
			const self = this;
			const data = self.getValues();
			data.action = 'eac_get_bill_address_html';
			self.block();
			$.post( ajaxurl, data, function ( r ) {
				self.unblock();
				const res = wpAjax.parseAjaxResponse( r, 'data' );
				if ( ! res || res.errors ) {
					self.$( '.document-address' ).html( '' );
					return;
				}
				self.$( '.document-address' ).html( res.responses[0].data );
			} );
		},

		onChangeCurrency( e ) {
			var currency = $( e.target ).val();
			var config = eac_currencies[ currency ] || eac_currencies[ eac_base_currency ];
			var $exchange = $( ':input[name="exchange_rate"]' );
			$exchange.val( config?.rate || 1 ).removeClass( 'enhanced' ).data( 'currency', currency ).attr( 'readonly', currency === eac_base_currency );
			$( document.body ).trigger( 'eac_update_ui' );
		},

		onAddItem( e ) {
			const self = this;
			const params = e.params.data;
			const data = self.getValues();
			// data.action = 'eac_get_bill_item_html';
			// self.block();
			// $.post( ajaxurl, data, function ( r ) {
			// 	self.unblock();
			// 	const res = wpAjax.parseAjaxResponse( r, 'data' );
			// 	if ( ! res || res.errors ) {
			// 		return;
			// 	}
			// 	self.$( '.items' ).append( res.responses[0].data );
			// } );
		},

		onChangeItem( e ) {
			const self = this;
			const data = self.getValues();
			data.action = 'eac_bill_recalculated_html';
			$.post( ajaxurl, data, function ( r ) {} );
		},

		onAddTax( e ) {
			const self = this;
			const params = e.params.data;
			const $row = $( e.target ).closest( '.eac-document-items__item' );
			const rowId = $row.data( 'id' );
			const nextIndex = _.uniqueId();
			const data = {
				...self.getValues(),
				['items[' + rowId + '][taxes][' + nextIndex + '][tax_id]']: params.id,
				['items[' + rowId + '][taxes][' + nextIndex + '][name]']: params.name,
				['items[' + rowId + '][taxes][' + nextIndex + '][rate]']: params.rate,
				['items[' + rowId + '][taxes][' + nextIndex + '][compound]']: params.compound || false,
			}

			console.log(data);
			self.updateTotals(data);
		},

		onRemoveTax( e ) {
			const self = this;
			const params = e.params.data;
			const $row = $( e.target ).closest( '.eac-document-items__item' );
			const rowId = $row.data( 'id' );
			const taxId = params.id;
			const data = {
				...self.getValues(),
				// loop through taxes and remove the one with the matching taxId.
				..._.omit( self.getValues().items || [], function ( value, key ) {
					console.log(key, value);
					return key.startsWith( 'items[' + rowId + '][taxes][][tax_id]' ) && value === taxId;
				} ),
			}
			self.updateTotals(data);
		},

		updateTotals( data ){
			const self = this;
			data || this.getValues();
			data.action = 'eac_bill_recalculated_html';
			$.post( ajaxurl, data, function ( r ) {
				console.log( r );
			} );
		}
	} );
} );
