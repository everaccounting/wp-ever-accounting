/**
 *  Adds common functionality to the window.
 *
 *  @param {jQuery} $        jQuery object.
 *  @param {Object} window   The window object.
 *  @param {mixed} undefined Unused.
 */
(function ($, window, undefined) {
	'use strict';
	var $document = $( document ),
		$window   = $( window ),
		$body     = $( document.body );

	window.EAC = {
		init: function () {
			var self = this;
			// Confirm delete action.
			$document.on(
				'click',
				'.eac_confirm_delete',
				function (e) {
					if ( ! self.confirm( eac_core_js_vars.i18n.confirm_delete )) {
						e.preventDefault();
					}
				}
			);
			// Input fields.
			self.inputNumber( '.eac_number_input' );
			self.inputDecimal( '.eac_decimal_input' );
		},
		confirm: function (message, callback) {
			if (confirm( message )) {
				callback();
			}
		},
		prompt: function (message, callback) {
			var response = prompt( message );
			if (response) {
				callback( response );
			}
		},
		flash: function (message, type) {
			type = type || 'success';
			$( '<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>' ).insertAfter( '.wrap h1' ).delay( 5000 ).fadeOut();
		},
		get: function (data, callback) {
			$.ajax(
				{
					url: ajaxurl,
					type: 'GET',
					data: data,
					xhrFields: {
						withCredentials: true
					},
					success: function (response) {
						callback( response );
					}
				}
			);
		},
		post: function (data, callback) {
			$.ajax(
				{
					url: ajaxurl,
					type: 'POST',
					data: data,
					xhrFields: {
						withCredentials: true
					},
					success: function (response) {
						callback( response );
					}
				}
			);
		},
		refresh: function () {
			window.location.reload();
		},
		redirect: function (url) {
			window.location.href
		},
		inputNumber: function (selector) {
			$( selector ).on(
				'input',
				function () {
					this.value = this.value.replace( /[^0-9]/g, '' );
				}
			);
		},
		inputDecimal: function (selector) {
			$( selector ).on(
				'input',
				function () {
					this.value = this.value.replace( /[^0-9.]/g, '' );
					// Remove multiple dots.
					this.value = this.value.replace( /\.{2,}/g, '.' );
					// Remove leading dots.
					this.value = this.value.replace( /^\./g, '' );
					// more than one decimal point.
					this.value = this.value.replace( /(\.\d*)\./g, '$1' );
				}
			);
		},
	}

	$(
		function () {
			EAC.init();
		}
	);

})( jQuery, window );
