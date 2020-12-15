/* global eaccounting_admin_i10n */
jQuery(function ($) {

	//initialize plugins
	function init_plugins(){
		$( '.ea-select2' ).eaccounting_select2();
		$('.ea-input-date').datepicker({dateFormat: 'yy-mm-dd'});
		$('.ea-help-tip').tipTip();
		eaccounting.mask_amount('.ea-input-price');
		eaccounting.mask_amount('#opening_balance');
		//$('#quantity').on('change keyup', eaccounting.number_input);
	}

	$(document)
		.ready(init_plugins)
		.on('ea_modal_ready', init_plugins)


	// $('.ea-input-price').inputmask('decimal', {
	// 	alias: 'numeric',
	// 	groupSeparator: ',',
	// 	autoGroup: true,
	// 	digits: 2,
	// 	radixPoint: '.',
	// 	digitsOptional: false,
	// 	allowMinus: false,
	// 	prefix: '',
	// 	placeholder: '0.000',
	// 	rightAlign: 0,
	// 	clearMaskOnLostFocus: false,
	// });


	// //status update
	// $( document ).on(
	// 	'click',
	// 	'.wp-list-table .ea_item_status_update',
	// 	function () {
	// 		var objectid = $( this ).data( 'object_id' ),
	// 			nonce = $( this ).data( 'nonce' ),
	// 			enabled = $( this ).is( ':checked' ) ? 1 : 0,
	// 			objecttype = $( this ).data( 'object_type' );
	//
	// 		if ( ! objectid || ! nonce || ! objecttype ) {
	// 			$.eaccounting_notice(
	// 				'Item Missing some important property',
	// 				'error'
	// 			);
	// 			return false;
	// 		}
	// 		wp.ajax.send( {
	// 			data: {
	// 				objectid: objectid,
	// 				nonce: nonce,
	// 				enabled: enabled,
	// 				objecttype: objecttype,
	// 				action: 'eaccounting_item_status_update',
	// 			},
	// 			success: function ( res ) {
	// 				$.eaccounting_notice( res, 'success' );
	// 			},
	// 			error: function ( error ) {
	// 				$.eaccounting_notice( error, 'error' );
	// 			},
	// 		} );
	// 	}
	// );
	//
	// //dropdwown
	// $( document )
	// 	.on( 'click', function () {
	// 		$( '.ea-dropdown' ).removeClass( 'open' );
	// 	} )
	// 	.on( 'click', '.ea-dropdown-button', function ( e ) {
	// 		e.preventDefault();
	// 		e.stopPropagation();
	// 		$( '.ea-dropdown' ).removeClass( 'open' );
	// 		$( this ).closest( '.ea-dropdown' ).toggleClass( 'open' );
	// 	} );

});
