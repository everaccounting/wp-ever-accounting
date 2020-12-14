jQuery(function ($) {
	'use strict';

	/**
	 * Ever Accounting Modal plugin
	 *
	 * @param {object} options
	 */
	$.fn.ea_modal = function (options) {
		return this.each(function () {
			new $.ea_modal(options);
		});
	};

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	var defaults = {
		title: '',
		target: '',
		button: 'Submit',
		onSubmit: undefined,
		onReady: undefined,
	};

	/**
	 * Initialize the Modal
	 *
	 * @param {object} options [description]
	 */
	$.ea_modal = function (options) {
		this.settings = $.extend(
			{},
			defaults,
			options
		);
		var plugin  = this;
		var $target = $(this.settings.target);
		if (!$target.length) {
			console.warn('Target element is not found.');
			return false;
		}

		var $modal = $('<div class="ea-modal">')
			.append(
				$('<div class="ea-modal__content">')
					.append($('<div class="ea-modal__relative">')
						.append($('<header class="ea-modal__header">')
							.prepend($('<h1 class="ea-modal__title">')
								.html(this.settings.title)
							)
							.append($('<button class="ea-modal__close dashicons">')
							)
						)
						.append($('<div class="ea-modal__body">')
							.append($target.html()
							)
						)
						.append($('<div class="ea-modal__footer">')
							.append($('<button>')
								.addClass('button button-primary ea-modal__submit-btn')
								.html(this.settings.button)
							)
						)
					)
			)
			.append($('<div class="ea-modal__backdrop ea-modal__close">'));

		$('.ea-modal__close', $modal).on('click', function (e) {
			e.preventDefault();
			plugin.close();
		});

		$('.ea-modal__submit-btn', $modal).on('click', function (e) {
			e.preventDefault();
			$('form [type="submit"]', $modal).trigger('click');
			console.log('Submit')
		});

		$( window ).resize( function () {
			//view.resizeContent();
		} );

		$modal.on('keydown', this.keyboardEvents);

		this.block = function () {
			$('.ea-modal__body', $modal).css({'opacity': 0.3});
			// $('#ajde_loading').fadeIn();
		}

		this.unblock = function () {
			$('.ea-modal__body', $modal).css({'opacity': 1});
			// $('#ajde_loading').fadeOut(20);
		}

		this.resize = function (){
			var $content = $( '.ea-modal__relative', $modal );
			var max_h = $( window ).height() * 0.75;

			$content.css( {
				'max-height': max_h + 'px',
			} );
		}

		this.keyboardEvents = function (e){
			var button = e.keyCode || e.which;
			console.log(button);
		}

		this.formData = function () {
			var $form = $('form', $modal);
			if ($form) {
				return $form.serializeObject();
			}
			return {};
		}

		this.get = function (name) {
			if (plugin.formData().hasOwnProperty(name)) {
				return plugin.form_data()[name];
			}
			return false;
		}

		this.close = function () {
			$( document.body ).trigger(
				'ea_modal_before_remove',
				plugin.settings.target
			);
			$( document ).off( 'focusin' );
			$( document.body )
				.css( {
					overflow: 'auto',
				} )
				.removeClass( 'ea-modal-open' );
			$('.ea-modal').remove();
			$( document.body ).trigger(
				'ea_modal_removed',
				plugin.settings.target
			);
		}

		this.init = function () {
			$( document.body )
				.css( {
					overflow: 'hidden',
				} )
				.append( $modal )
				.addClass( 'ea-modal-open' );
			$( '.ea-modal-content', $modal )
				.attr( 'tabindex', '0' )
				.focus();

			$( document.body ).trigger(
				'ea_modal_loaded',
				this.settings.target
			);
			$( document.body ).trigger( this.settings.target + '_loaded' );
			if ( typeof this.settings.onReady === 'function' ) {
				this.settings.onReady( $modal, this );
			}

			if ( typeof this.settings.onSubmit === 'function' ) {
				$( 'form', $modal ).on( 'submit', function ( e ) {
					e.preventDefault();
					plugin.block();
					plugin.settings.onSubmit( $modal, plugin );
				} );
			}
		}

		this.init();
	}

	// $.ea_modal({
	// 	target: '#modal-add-invoice-item',
	// 	title: 'Add Item'
	// });

});
