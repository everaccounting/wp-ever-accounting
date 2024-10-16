jQuery( document ).ready( ( $ ) => {
	'use strict';

	$( '#eac-edit-expense' ).eac_form( {
		events: {
			'change :input[name="account_id"]': 'handleExchangeRate',
			'change :input[name="bill_id"]': 'onChangeBill',
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
		onChangeBill: function (e) {
			// var self = this;
			// var $bill = this.$( ':input[name="bill_id"]' );
			// var bill_id = $bill.val();
			//
			// // bail if no bill selected.
			// if ( ! bill_id ) {
			// 	return;
			// }
			//
			// self.block();
			// var Bill = new eac_api.Bill( { id: bill_id } );
			// Bill.fetch().then( function ( json ) {
			// 	const data = Object.keys( json )
			// 		.filter( ( key ) => self.options.state.toJSON().hasOwnProperty( key ) )
			// 		.reduce( ( acc, key ) => {
			// 			acc[ key ] = json[ key ];
			// 			return acc;
			// 		}, {} );
			// 	self.options.state.set( data );
			// 	self.unblock();
			// } );
		},
	} );
});
