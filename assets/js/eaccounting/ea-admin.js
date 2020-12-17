/* global eaccounting_admin_i10n */
jQuery(function ($) {

	//initialize plugins
	$('.ea-input-date').datepicker({dateFormat: 'yy-mm-dd'});
	$('.ea-help-tip').tipTip();
	eaccounting.mask_amount('.ea-input-price');
	eaccounting.mask_amount('#opening_balance');
	//eaccounting.dropdown('.ea-dropdown');
	$(document.body).trigger('ea_select2_init');
	$('#quantity').on('change keyup', function (e){
		e.target.value = e.target.value.replace(/[^0-9.]/g, '');
	});
	$(document.body).on('ea_modal_loaded', function (){
		$(document.body).trigger('ea_select2_init');
	});


	var frame = false;
	$('.ea-upload-attachment').on('click', function (e) {
		e.preventDefault();
		var $button = $(this);
		if (frame) {
			frame.open();
			return false;
		}

		frame = wp.media({
			title: 'Select or upload image',
			button: {
				text: 'Select',
			},
			library: {
				type: 'image',
			},
			multiple: false,
			custom: 'custom'
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			$button.siblings('.ea-file-input').eq(0).val(attachment.id);
			$button.siblings('.ea-file').find('.ea-file-link').attr('href', attachment.url).text(attachment.filename);
			$button.siblings('.ea-file').show();
			$button.hide();
		});

		frame.on('ready', function () {
			frame.uploader.options.uploader.params = {
				type: 'eaccounting_file'
			};
			console.log(frame.uploader.options.uploader.params);
		});

		frame.open();
	});
	$('.ea-file-delete').on('click', function (e) {
		e.preventDefault();
		var $button = $(this);
		$button.closest('.ea-file').siblings('.ea-file-input').eq(0).val('');
		$button.closest('.ea-file').find('.ea-file-link').attr('href', '').text();
		$button.closest('.ea-file').hide();
		$button.closest('.ea-file').siblings('.ea-upload-attachment').show();
	})

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
