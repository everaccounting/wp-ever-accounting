/*global jQuery, Backbone, _ */
( function ( $, Backbone, _ ) {
	'use strict';

	/**
	 * WooCommerce Backbone Modal plugin
	 *
	 * @param {object} options
	 */
	$.fn.ea_modal = function ( options ) {
		return this.each( function () {
			new $.ea_modal( $( this ), options );
		} );
	};

	/**
	 * Initialize the Backbone Modal
	 *
	 * @param {object} element [description]
	 * @param {object} options [description]
	 */
	$.ea_modal = function ( element, options ) {
		// Set settings
		var settings = $.extend(
			{},
			$.ea_modal.defaultOptions,
			options
		);
		if ( settings.template ) {
			new $.ea_modal.View( {
				target: settings.template,
				string: settings.variable,
				onSubmit: options.onSubmit,
				onReady: options.onReady,
			} );
		}
	};

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	$.ea_modal.defaultOptions = {
		template: '',
		variable: {},
		onSubmit: undefined,
		onReady: undefined,
	};

	/**
	 * Create the Backbone Modal
	 *
	 * @return {null}
	 */
	$.ea_modal.View = Backbone.View.extend( {
		tagName: 'div',
		id: 'ea-modal-dialog',
		_target: undefined,
		_string: undefined,
		onSubmit: undefined,
		onReady: undefined,
		events: {
			'click .modal-close': 'closeButton',
			'touchstart #btn-ok': 'addButton',
			keydown: 'keyboardActions',
		},
		resizeContent: function () {
			var $content = $( '.ea-modal-content' ).find( 'article' );
			var max_h = $( window ).height() * 0.75;

			$content.css( {
				'max-height': max_h + 'px',
			} );
		},
		initialize: function ( data ) {
			var view = this;
			this._target = data.target;
			this._string = data.string;
			this.onSubmit = data.onSubmit;
			this.onReady = data.onReady;
			_.bindAll( this, 'render' );
			this.render();

			$( window ).resize( function () {
				view.resizeContent();
			} );
		},
		render: function () {
			var template = wp.template( this._target );

			this.$el.append( template( this._string ) );

			$( document.body )
				.css( {
					overflow: 'hidden',
				} )
				.append( this.$el )
				.addClass( 'ea-modal-open' );

			this.resizeContent();
			this.$( '.ea-modal-content' )
				.attr( 'tabindex', '0' )
				.focus();

			$( document.body ).trigger( 'init_tooltips' );

			$( document.body ).trigger(
				'ea_modal_loaded',
				this._target
			);
			$( document.body ).trigger( this._target + '_loaded' );
			var modal = this;
			if ( typeof this.onReady === 'function' ) {
				this.onReady( this.$el, this );
			}
			if ( typeof this.onSubmit === 'function' ) {
				this.$el.find( 'form' ).on( 'submit', function ( e ) {
					e.preventDefault();
					modal.onSubmit( modal.getFormData(), modal );
				} );
			}
		},
		closeButton: function ( e ) {
			e.preventDefault();
			this.closeModal();
		},
		closeModal: function () {
			$( document.body ).trigger(
				'ea_modal_before_remove',
				this._target
			);
			this.undelegateEvents();
			$( document ).off( 'focusin' );
			$( document.body )
				.css( {
					overflow: 'auto',
				} )
				.removeClass( 'ea-modal-open' );
			this.remove();
			$( document.body ).trigger(
				'ea_modal_removed',
				this._target
			);
		},
		getFormData: function () {
			var data = {};
			$.each( $( 'form', this.$el ).serializeArray(), function (
				index,
				item
			) {
				if ( item.name.indexOf( '[]' ) !== -1 ) {
					item.name = item.name.replace( '[]', '' );
					data[ item.name ] = $.makeArray( data[ item.name ] );
					data[ item.name ].push( item.value );
				} else {
					data[ item.name ] = item.value;
				}
			} );

			return data;
		},
		keyboardActions: function ( e ) {
			var button = e.keyCode || e.which;

			// Enter key
			if ( 13 === button && this.$el.find( 'form' ).length ) {
				e.preventDefault();
				this.$el.find( '[type="submit"]' ).trigger( 'click' );
				return false;
			}

			// ESC key
			if ( 27 === button ) {
				this.closeButton( e );
			}
		},
		disableSubmit: function () {
			this.$el.find( '*[type="submit"]' ).attr( 'disabled', 'disabled' );
		},
		enableSubmit: function () {
			this.$el.find( '*[type="submit"]' ).removeAttr( 'disabled' );
		},
	} );
} )( jQuery, Backbone, _ );