jQuery( document ).ready( ( $ ) => {
	'use strict';

	$( '#eac-edit-payment' ).eac_form( {
		events: {
			'change :input[name="account_id"]': 'handleExchangeRate',
		},
		handleExchangeRate: function () {
			var self = this;
			var $amount = this.$( ':input[name="amount"]' );
			var $conversion = this.$( ':input[name="exchange_rate"]' );
			var account_id = this.$( ':input[name="account_id"]' ).val();

			if ( ! account_id ) {
				$conversion.val( 1.0 );
				$conversion.attr( 'readonly', true ).val( 1.0 );
				return;
			}

			self.block();
			var Account = new eac_api.Account( { id: account_id } );
			Account.fetch( {
				success: function ( account ) {
					var new_currency = account.get( 'currency' ) || eac_base_currency;
					$amount.data( 'currency', new_currency ).removeClass( 'enhanced' );
					$conversion.val( eac_currencies[ new_currency ].rate || 1.0 );
					$conversion.attr( 'readonly', new_currency === eac_base_currency );
					$( document.body ).trigger( 'eac_update_ui' );
				},
			} ).then( function () {
				self.unblock();
			} );
		},
	} );

	$( '#eac-update-payment' ).eac_form( {
		events: {
			'keyup :input#payment-note': 'onChangeNote',
			'change :input#payment-note': 'onChangeNote',
			'click button.add-note': 'onAddNote',
		},

		onChangeNote: function ( e ) {
			const $submit = this.$( 'button.add-note' );
			const note = this.$( e.target ).val();
			$submit.prop( 'disabled', ! note );
		},

		onAddNote: function ( e ) {
			const self = this;
			const payment_id = this.$( ':input[name="id"]' ).val();
			const content = this.$( ':input#payment-note' ).val();
			// if any of the required fields are empty then return.
			if ( ! payment_id || ! content ) {
				return;
			}

			self.block();
			const Note = new eac_api.Note();
			Note.save(
				{
					content,
					parent_id: payment_id,
					parent_type: 'payment',
				},
				{
					method: 'POST',
				}
			).then( ( json ) => {
				self.$( ':input#payment-note' ).val( '' );
				self.unblock();
				// $('li')
				// 	.addClass('note')
				// 	.append('<div class="note__header">
				// const notes = self.$('ul#payment-notes')
				// notes.append(  $( '<li class="note">' ).html( json.content ) );
			} );
		},
	} );
});
