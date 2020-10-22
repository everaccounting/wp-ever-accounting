/* global eaccounting_admin_i10n */
jQuery( document ).ready( function ( $ ) {
	//initialize plugins
	$( '.ea-input-date' ).datepicker( { dateFormat: 'yy-mm-dd' } );
	$( '.ea-help-tip' ).tipTip();
	$( '.ea-input-price' ).inputmask( 'decimal', {
		alias: 'numeric',
		groupSeparator: ',',
		autoGroup: true,
		digits: 2,
		radixPoint: '.',
		digitsOptional: false,
		allowMinus: false,
		prefix: '',
		placeholder: '0.000',
		rightAlign: 0,
		clearMaskOnLostFocus: false,
	} );

	$( '.ea-date-range-picker' )
		.daterangepicker( {
			autoUpdateInput: false,
			locale: eaccounting_i10n.datepicker.locale,
			ranges: {
				Today: [ moment(), moment() ],
				Yesterday: [
					moment().subtract( 1, 'days' ),
					moment().subtract( 1, 'days' ),
				],
				'Last 7 Days': [ moment().subtract( 6, 'days' ), moment() ],
				'Last 30 Days': [ moment().subtract( 29, 'days' ), moment() ],
				'This Month': [
					moment().startOf( 'month' ),
					moment().endOf( 'month' ),
				],
				'Last Month': [
					moment().subtract( 1, 'month' ).startOf( 'month' ),
					moment().subtract( 1, 'month' ).endOf( 'month' ),
				],
			},
		} )
		.on( 'apply.daterangepicker', function ( ev, picker ) {
			var format = eaccounting_i10n.datepicker.locale.format;
			var sep = eaccounting_i10n.datepicker.locale.separator;
			$( this )
				.find( 'span' )
				.html(
					picker.startDate.format( format ) +
						sep +
						picker.endDate.format( format )
				);
			$( this )
				.find( '[name="start_date"]' )
				.val( picker.startDate.format( 'YYYY-MM-DD' ) );
			$( this )
				.find( '[name="end_date"]' )
				.val( picker.endDate.format( 'YYYY-MM-DD' ) );
		} )
		.on( 'cancel.daterangepicker', function ( ev, picker ) {
			$( this ).find( 'span' ).html( '' );
			$( this ).find( '[name="start_date"]' ).val( '' );
			$( this ).find( '[name="end_date"]' ).val( '' );
		} );

	//status update
	$( document ).on(
		'click',
		'.wp-list-table .ea_item_status_update',
		function () {
			var objectid = $( this ).data( 'object_id' ),
				nonce = $( this ).data( 'nonce' ),
				enabled = $( this ).is( ':checked' ) ? 1 : 0,
				objecttype = $( this ).data( 'object_type' );

			if ( ! objectid || ! nonce || ! objecttype ) {
				$.eaccounting_notice(
					'Item Missing some important property',
					'error'
				);
				return false;
			}
			wp.ajax.send( {
				data: {
					objectid: objectid,
					nonce: nonce,
					enabled: enabled,
					objecttype: objecttype,
					action: 'eaccounting_item_status_update',
				},
				success: function ( res ) {
					$.eaccounting_notice( res, 'success' );
				},
				error: function ( error ) {
					$.eaccounting_notice( error, 'error' );
				},
			} );
		}
	);

	//dropdwown
	$( document )
		.on( 'click', function () {
			$( '.ea-dropdown' ).removeClass( 'open' );
		} )
		.on( 'click', '.ea-dropdown-button', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			$( '.ea-dropdown' ).removeClass( 'open' );
			$( this ).closest( '.ea-dropdown' ).toggleClass( 'open' );
		} );

	//forms
	$( '.ea-ajax-form' ).eaccounting_form();

	//creatable form
	$( '.eaccounting form #currency_code' ).eaccounting_creatable( {
		option: function ( item ) {
			return {
				id: item.code,
				text: item.name + ' (' + item.symbol + ')',
			};
		},
	} );
	$( '#account_id' ).eaccounting_creatable();
	$( '#customer_id' ).eaccounting_creatable( {
		onReady: function ( $el, $modal ) {
			$( '#type', $modal.$el ).val( 'customer' );
		},
	} );
	$( '#vendor_id' ).eaccounting_creatable( {
		onReady: function ( $el, $modal ) {
			$( '#type', $modal.$el ).val( 'vendor' );
		},
	} );
	$( '#category_id' ).eaccounting_creatable( {
		onReady: function ( $el, $modal ) {
			var type = $el.data( 'type' );
			if ( ! type ) {
				console.warn( 'No category type defined' );
			}
			$( '#type', $modal.$el ).val( type.replace( '_category', '' ) );
		},
	} );
} );
