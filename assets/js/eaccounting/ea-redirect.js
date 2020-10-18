
/**
 * Redirect plugin for EverAccounting.
 * @since 1.0.2
 */
jQuery( function ( $ ) {
	$.eaccounting_redirect = function ( url ) {
		if ( 'object' === typeof url ) {
			if ( ! ( 'redirect' in url ) ) {
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

	$.fn.eaccounting_redirect = function ( url ) {
		return new $.eaccounting_redirect( url );
	};
} );
