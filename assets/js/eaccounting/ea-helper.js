window.eaccounting = window.eaccounting || {}

jQuery(function ($) {
	'use strict';
	/**
	 * A nifty plugin to converting form to serialize object
	 */
	$.fn.serializeObject = function () {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name] !== undefined) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	/**
	 * A plugin for converting form to serializeAssoc
	 * @returns {{}}
	 */
	$.fn.serializeAssoc = function () {
		var data = {};
		$.each(this.serializeArray(), function (key, obj) {
			var a = obj.name.match(/(.*?)\[(.*?)\]/);
			if (a !== null) {
				var subName = a[1];
				var subKey = a[2];

				if (!data[subName]) {
					data[subName] = [];
				}

				if (!subKey.length) {
					subKey = data[subName].length;
				}

				if (data[subName][subKey]) {
					if (Array.isArray(data[subName][subKey])) {
						data[subName][subKey].push(obj.value);
					} else {
						data[subName][subKey] = [];
						data[subName][subKey].push(obj.value);
					}
				} else {
					data[subName][subKey] = obj.value;
				}
			} else {
				if (data[obj.name]) {
					if (Array.isArray(data[obj.name])) {
						data[obj.name].push(obj.value);
					} else {
						data[obj.name] = [];
						data[obj.name].push(obj.value);
					}
				} else {
					data[obj.name] = obj.value;
				}
			}
		});
		return data;
	};


	$.fn.eaccounting_redirect = function ( url ) {
		return new $.eaccounting_redirect( url );
	};

	$.eaccounting_redirect = function ( url ) {
		if ('object' === typeof url) {
			if (('data' in url)) {
				url = url.data;
			}

			if (!('redirect' in url)) {
				return;
			}
			url = url.redirect;
		}

		if ( ! url ) {
			return;
		}
		url = url.trim();
		if ( ! url ) {
			return false;
		}
		var ua = navigator.userAgent.toLowerCase(),
			isIE = ua.indexOf( 'msie' ) !== -1,
			version = parseInt( ua.substr( 4, 2 ), 10 );

		// Internet Explorer 8 and lower
		if ( isIE && version < 9 ) {
			var link = document.createElement( 'a' );
			link.href = url;
			document.body.appendChild( link );
			return link.click();
		}
		// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
		window.location.href = url;
	};

	$.fn.eaccounting_notice = function ( message, type, options ) {
		return this.each( function () {
			message = message.trim();
			if ( message ) {
				new $.eaccounting_notice( message, type, options );
			}
		} );
	};

	$.eaccounting_notice = function ( message, type, options ) {
		if ( 'object' === typeof message ) {
			if ( ( 'data' in message ) ) {
				if( message.success && true === message.success ){
					type = 'success';
				}else {
					type = 'error';
				}
				message = message.data;
			}

			if ( ! ( 'message' in message ) ) {
				return;
			}

			message = message.message;
		}

		if ( ! message ) {
			return;
		}
		message = message.trim();
		if ( ! message ) {
			return false;
		}
		options = $.extend(
			true,
			{},
			$.eaccounting_notice.defaultOptions,
			options
		);

		var html =
			'<div class="eaccounting-notice notice notice-' +
			( type ? type : options.type ) +
			' ' +
			( options.customClass ? options.customClass : '' ) +
			'">';
		html += message;
		html += '</div>';

		var offsetSum = options.offset.amount;
		if ( ! options.stack ) {
			$( '.eaccounting-notice' ).each( function () {
				return ( offsetSum = Math.max(
					offsetSum,
					parseInt( $( this ).css( options.offset.from ) ) +
					this.offsetHeight +
					options.spacing
				) );
			} );
		} else {
			$( options.appendTo )
				.find( '.eaccounting-notice' )
				.each( function () {
					return ( offsetSum = Math.max(
						offsetSum,
						parseInt( $( this ).css( options.offset.from ) ) +
						this.offsetHeight +
						options.spacing
					) );
				} );
		}

		var css = {
			position: options.appendTo === 'body' ? 'fixed' : 'absolute',
			margin: 0,
			'z-index': '9999',
			display: 'none',
			'min-width': options.minWidth,
			'max-width': options.maxWidth,
		};

		css[ options.offset.from ] = offsetSum + 'px';

		var $notice = $( html ).css( css ).appendTo( options.appendTo );

		switch ( options.align ) {
			case 'center':
				$notice.css( {
					left: '50%',
					'margin-left': '-' + $notice.outerWidth() / 2 + 'px',
				} );
				break;
			case 'left':
				$notice.css( 'left', '20px' );
				break;
			default:
				$notice.css( 'right', '20px' );
		}

		if ( $notice.fadeIn ) $notice.fadeIn();
		else $notice.css( { display: 'block', opacity: 1 } );

		function removeAlert() {
			$.eaccounting_notice.remove( $notice );
		}

		if ( options.delay > 0 ) {
			setTimeout( removeAlert, options.delay );
		}

		$notice.click( removeAlert );

		return $notice;
	};

	$.eaccounting_notice.remove = function ( $alert ) {
		if ( $alert.fadeOut ) {
			return $alert.fadeOut( function () {
				return $alert.remove();
			} );
		} else {
			return $alert.remove();
		}
	};

	$.eaccounting_notice.defaultOptions = {
		appendTo: 'body',
		stack: false,
		customClass: false,
		type: 'success',
		offset: {
			from: 'top',
			amount: 50,
		},
		align: 'right',
		minWidth: 250,
		maxWidth: 450,
		delay: 4000,
		spacing: 10,
	};

	eaccounting.mask_amount = function (input, currency) {
		currency = currency || {};
		$(input).inputmask('decimal', {
			alias: 'numeric',
			groupSeparator: currency.thousand_separator || ',',
			autoGroup: true,
			digits: currency.precision || 2,
			radixPoint: currency.decimal_separator || '.',
			digitsOptional: false,
			allowMinus: false,
			prefix: currency.symbol || '',
			placeholder: '0.000',
			rightAlign: 0,
		})
	}
})
