(function ($, window, document, undefined) {
	'use strict';
	/**
	 * A nifty plugin to converting form to serialize object
	 */
	$.fn.serializeObject = function () {
		const o = {};
		const a = this.serializeArray();
		$.each( a, function () {
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
	 *
	 * @return {{}}
	 */
	$.fn.serializeAssoc = function () {
		const data = {};
		$.each( this.serializeArray(), function ( key, obj ) {
			const a = obj.name.match( /(.*?)\[(.*?)\]/ );
			if ( a !== null ) {
				const subName = a[ 1 ];
				let subKey = a[ 2 ];

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
			} else if ( data[ obj.name ] ) {
				if ( Array.isArray( data[ obj.name ] ) ) {
					data[ obj.name ].push( obj.value );
				} else {
					data[ obj.name ] = [];
					data[ obj.name ].push( obj.value );
				}
			} else {
				data[ obj.name ] = obj.value;
			}
		} );
		return data;
	};

}(jQuery, window, document));

(function ($, window, document, undefined) {
	'use strict';
	$.eac_select2 = function (el, options) {
		this.el = el;
		this.$el = $(el);
		this.options = $.extend({
			minimumResultsForSearch: 10,
			allowClear: this.$el.data('allow_clear') || true,
			placeholder: this.$el.data('placeholder')||'',
			minimumInputLength: this.$el.data('minimum_input_length') ? this.$el.data('minimum_input_length') : 0,
		}, options);

		// When multiple set allowClear to false.
		if (this.$el.prop('multiple')){
			this.options.allowClear = false;
		}

		// if the select2 is within a drawer, then set the parent to the drawer.
		if (this.$el.closest('.eac-form').length) {
			this.options.dropdownParent = this.$el.closest('.eac-form');
		}

		return this.$el.select2(this.options);
	};
	$.fn.eac_select2 = function (options) {
		return this.each(function () {
			if (!$(this).data('select2')) {
				new $.eac_select2(this, $.extend({}, $(this).data(), options));
			}
		});
	};


}(jQuery, window, document));

(function ($, window, document, undefined) {
	'use strict';
	/**
	 * Redirect to a URL.
	 * @param url string|object URL to redirect to.
	 * @returns {*|boolean}
	 */
	$.eac_redirect = function (url) {
		if ('object' === typeof url) {
			if ('data' in url) {
				url = url.data;
			}

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
		const ua = navigator.userAgent.toLowerCase(),
			isIE = ua.indexOf('msie') !== -1,
			version = parseInt(ua.substr(4, 2), 10);

		// Internet Explorer 8 and lower
		if (isIE && version < 9) {
			const link = document.createElement('a');
			link.href = url;
			document.body.appendChild(link);
			return link.click();
		}
		// All other browsers can use the standard window.location.href (they don't lose HTTP_REFERER like Internet Explorer 8 & lower does)
		window.location.href = url;
	};

	$.fn.eac_redirect = function (url) {
		return new $.eac_redirect(url);
	};

}(jQuery, window, document));

(function ($, window, document, undefined) {
	'use strict';
	$.eac_notification = function (message, type, options) {
		var defaults = {
			appendTo: 'body',
			stack: false,
			customClass: false,
			type: 'success',
			offset: {
				from: 'bottom',
				amount: 50,
			},
			align: 'right',
			minWidth: 250,
			maxWidth: 450,
			delay: 4000,
			spacing: 10,
		}
		if ('object' === typeof message) {
			if ('data' in message) {
				if (message.success && true === message.success) {
					type = 'success';
				} else {
					type = 'error';
				}
				message = message.data;
			}

			if (!('message' in message)) {
				return;
			}

			message = message.message;
		}

		if (!message) {
			return;
		}
		message = message.trim();
		if (!message) {
			return false;
		}
		options = $.extend(
			true,
			{},
			defaults,
			options
		);

		let html =
			'<div class="eac-notification notice notice-' +
			(type ? type : options.type) +
			' ' +
			(options.customClass ? options.customClass : '') +
			'">';
		html += message;
		html += '</div>';

		let offsetSum = options.offset.amount;
		if (!options.stack) {
			$('.eac-notification').each(function () {
				return (offsetSum = Math.max(
					offsetSum,
					parseInt($(this).css(options.offset.from)) +
					this.offsetHeight +
					options.spacing
				));
			});
		} else {
			$(options.appendTo)
				.find('.eac-notification')
				.each(function () {
					return (offsetSum = Math.max(
						offsetSum,
						parseInt($(this).css(options.offset.from)) +
						this.offsetHeight +
						options.spacing
					));
				});
		}

		const css = {
			position: options.appendTo === 'body' ? 'fixed' : 'absolute',
			margin: 0,
			'z-index': '9999',
			display: 'none',
			'min-width': options.minWidth,
			'max-width': options.maxWidth,
		};

		css[options.offset.from] = offsetSum + 'px';

		const $notice = $(html).css(css).appendTo(options.appendTo);

		switch (options.align) {
			case 'center':
				$notice.css({
					left: '50%',
					'margin-left': '-' + $notice.outerWidth() / 2 + 'px',
				});
				break;
			case 'left':
				$notice.css('left', '20px');
				break;
			default:
				$notice.css('right', '20px');
		}

		if ($notice.fadeIn) $notice.fadeIn();
		else $notice.css({display: 'block', opacity: 1});

		function removeAlert() {
			$.eac_notification.remove($notice);
		}

		if (options.delay > 0) {
			setTimeout(removeAlert, options.delay);
		}

		$notice.click(removeAlert);

		return $notice;
	};

	$.eac_notification.remove = function ($alert) {
		if ($alert.fadeOut) {
			return $alert.fadeOut(function () {
				return $alert.remove();
			});
		}
		return $alert.remove();
	};

	$.fn.eac_notification = function (message, type, options) {
		return this.each(function () {
			message = message.trim();
			if (message) {
				new $.eac_notification(message, type, options);
			}
		});
	};


}(jQuery, window, document));
