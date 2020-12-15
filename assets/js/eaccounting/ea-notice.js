jQuery( function ( $ ) {
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
} );
