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
			locale: eaccounting_admin_i10n.datepicker.locale,
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
			var format = eaccounting_admin_i10n.datepicker.locale.format;
			var sep = eaccounting_admin_i10n.datepicker.locale.separator;
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

} );


/**
 * A nifty plugin to converting form to serialize object
 */
jQuery.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	jQuery.each( a, function () {
		if ( o[ this.name ] !== undefined ) {
			if ( ! o[ this.name ].push ) {
				o[ this.name ] = [ o[ this.name ] ];
			}
			o[ this.name ].push( this.value || '' );
		} else {
			o[ this.name ] = this.value || '';
		}
	} );
	return o;
};

/**
 * A plugin for converting form to serializeAssoc
 * @returns {{}}
 */
jQuery.fn.serializeAssoc = function () {
	var data = {};
	jQuery.each( this.serializeArray(), function ( key, obj ) {
		var a = obj.name.match( /(.*?)\[(.*?)\]/ );
		if ( a !== null ) {
			var subName = a[ 1 ];
			var subKey = a[ 2 ];

			if ( ! data[ subName ] ) {
				data[ subName ] = [];
			}

			if ( ! subKey.length ) {
				subKey = data[ subName ].length;
			}

			if ( data[ subName ][ subKey ] ) {
				if ( $.isArray( data[ subName ][ subKey ] ) ) {
					data[ subName ][ subKey ].push( obj.value );
				} else {
					data[ subName ][ subKey ] = [];
					data[ subName ][ subKey ].push( obj.value );
				}
			} else {
				data[ subName ][ subKey ] = obj.value;
			}
		} else {
			if ( data[ obj.name ] ) {
				if ( $.isArray( data[ obj.name ] ) ) {
					data[ obj.name ].push( obj.value );
				} else {
					data[ obj.name ] = [];
					data[ obj.name ].push( obj.value );
				}
			} else {
				data[ obj.name ] = obj.value;
			}
		}
	} );
	return data;
};



/**
 * Color field wrapper for Ever Accounting
 * @since 1.0.2
 */
jQuery( function ( $ ) {
	jQuery.fn.ea_color_picker = function () {
		return this.each( function () {
			var el = this;
			$( el )
				.iris( {
					change: function ( event, ui ) {
						$( el )
							.parent()
							.find( '.colorpickpreview' )
							.css( { backgroundColor: ui.color.toString() } );
					},
					hide: true,
					border: true,
				} )
				.on( 'click focus', function ( event ) {
					event.stopPropagation();
					$( '.iris-picker' ).hide();
					$( el ).closest( 'div' ).find( '.iris-picker' ).show();
					$( el ).data( 'original-value', $( el ).val() );
				} )
				.on( 'change', function () {
					if ( $( el ).is( '.iris-error' ) ) {
						var original_value = $( this ).data( 'original-value' );

						if (
							original_value.match(
								/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/
							)
						) {
							$( el )
								.val( $( el ).data( 'original-value' ) )
								.change();
						} else {
							$( el ).val( '' ).change();
						}
					}
				} );

			$( 'body' ).on( 'click', function () {
				$( '.iris-picker' ).hide();
			} );
		} );
	};

	$( '.ea-input-color' ).ea_color_picker();
} );
