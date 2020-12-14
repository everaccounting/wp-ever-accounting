/**
 * Handle the creation of new items from dropdown
 * since 1.0.2
 */
jQuery( function ( $ ) {
	$.fn.eaccounting_creatable = function ( options ) {
		return this.each( function () {
			new $.eaccounting_creatable( this, options );
		} );
	};

	$.eaccounting_creatable = function ( el, options ) {
		this.defaults = {
			option: function ( item ) {
				return { id: item.id, text: item.name };
			},
			template: undefined,
			onReady: undefined,
			onSubmit: undefined,
		};
		this.el = el;
		this.$el = $( el );
		this.options = $.extend( this.defaults, options );
		var self = this;
		this.block = function () {
			self.$el.block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
		};

		this.unblock = function () {
			self.$el.unblock();
		};

		this.onError = function ( error ) {
			console.warn( error );
			self.unblock();
		};

		this.init_plugins = function () {
			$( '.ea-select2', this.$el ).eaccounting_select2();
			$( '.ea-input-color', this.$el ).ea_color_picker();
		};

		this.handleSubmit = function ( formData, $modal ) {
			self.block();
			$modal.disableSubmit();

			if ( typeof self.options.onSubmit === 'function' ) {
				self.options.onSubmit( self.$el, formData, $modal );
			}

			wp.ajax.send( {
				data: formData,
				success: function ( res ) {
					var option = self.options.option( res.item );
					self.$el.eaccounting_select2( { data: [ option ] } );
					self.$el.val( option.id ).trigger( 'change' );
					$.eaccounting_notice( res.message, 'success' );
					$modal.closeModal();
					$modal.enableSubmit();
				},
				error: function ( error ) {
					$.eaccounting_notice( error.message, 'error' );
					$modal.enableSubmit();
				},
			} );
		};
		this.handleModal = function ( e, $el, template ) {
			e.preventDefault();
			if ( $el.is( self.$el ) ) {
				e.preventDefault();
				$( this ).ea_modal( {
					template: 'ea-modal-' + template,
					onSubmit: self.handleSubmit,
					onReady: function ( el, $modal ) {
						if ( typeof self.options.onReady === 'function' ) {
							self.options.onReady( self.$el, $modal, self );
						}
					},
				} );
			}
		};

		this.init = function () {
			$( document )
				.on( 'ea_trigger_creatable', self.handleModal )
				.on( 'ea_modal_loaded', self.init_plugins );
		};

		this.init();

		return this;
	};

	//creatable form
	$( '#currency_code' ).eaccounting_creatable( {
		option: function ( item ) {
			return {
				id: item.code,
				text: item.name + ' (' + item.symbol + ')',
			};
		},
	} );

	$( '#account_id,#customer_id,#vendor_id' ).eaccounting_creatable();

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
