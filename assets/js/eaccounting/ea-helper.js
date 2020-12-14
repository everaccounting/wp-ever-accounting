window.eaccounting = window.eaccounting || {}

eaccounting.parse_decimal = function (number) {
	return number.replace(/[^0-9.]/g, '');
}

eaccounting.parse_number = function (number, decimal) {
	decimal = decimal || false;
	var value = number.replace(/[^0-9.]/g, '');
	return decimal ? parseFloat(value) : parseInt(value, 10);
}

eaccounting.block = function (el) {
	// if (!jQuery.isFunction($.blockUI)) {
	// 	console.warn('Block UI not loaded');
	// 	return false;
	// }
	jQuery(el).block({
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});
}

eaccounting.unblock = function (el) {
	// if (!jQuery().blockUI) {
	// 	console.warn('Block UI not loaded');
	// 	return false;
	// }
	jQuery(el).unblock();
}

eaccounting.redirect = function (url) {
	if ('object' === typeof url) {
		if (!('redirect' in url)) {
			return;
		}
		url = url.redirect;
	}

	if (!url) {
		return;
	}
	url = url.trim();
	if (!url) {
		return false;
	}
	var ua = navigator.userAgent.toLowerCase(),
		isIE = ua.indexOf('msie') !== -1,
		version = parseInt(ua.substr(4, 2), 10);

	// Internet Explorer 8 and lower
	if (isIE && version < 9) {
		var link = document.createElement('a');
		link.href = url;
		document.body.appendChild(link);
		return link.click();
	}
	// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
	window.location.href = url;
}

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
				if ( Array.isArray( data[ subName ][ subKey ] ) ) {
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
				if ( Array.isArray( data[ obj.name ] ) ) {
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
