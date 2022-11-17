/**
 * Select2 wrapper for EverAccounting
 *
 * The plugin is created to handle ajax search
 * & create item on the fly
 *
 * @since 1.0.2
 */

jQuery(
	function ( $ ) {
		$.fn.eaccounting_select2 = function ( options ) {
			return this.each(
				function () {
					new $.eaccounting_select2(
						this,
						$.extend( {}, $( this ).data(), options )
					);
				}
			);
		};
		$.eaccounting_select2    = function ( el, options ) {
			  this.el      = el;
			  this.$el     = $( el );
			  this.options = options;
			  this.id      = this.$el.attr( 'id' );
			  const plugin = this;
			if ( options.ajax_action ) {
				options.ajax        = {
					cache: true,
					delay: 500,
					url: options.url,
					method: 'POST',
					dataType: 'json',
					data( params ) {
						return {
							action: plugin.options.ajax_action,
							_wpnonce: plugin.options.nonce,
							search: params.term,
							page: params.page,
						};
					},
					processResults( json, params ) {
						params.page = params.page || 1;
						const map   =
						plugin.options.map ||
						'return {text: option.name, id:option.id}';
						const fn    = new Function( 'option', map );
						return {
							results: json.data.map(
								function ( option ) {
									return fn( option );
								}
							),
						pagination: false,
						};
					},
				};
			}

			this.$el.select2( options );

			if ( this.options.modal_id ) {
				this.$el.on(
					'select2:open',
					function ( e ) {
						const $results = $( this ).data( 'select2' ).$results;
						if ( ! $results.siblings( '.ea-select2-footer' ).length ) {
							const $footer = $(
								'<a href="#" class="ea-select2-footer"><span class="dashicons dashicons-plus">&nbsp;</span>' +
								plugin.options.add_text +
								'</a>'
							).on(
								'click',
								function ( e ) {
									e.preventDefault();
									plugin.$el.select2( 'close' );
									$( plugin.options.modal_id ).ea_modal();
								}
							);
								   $results.closest( '.select2-results' ).append( $footer );
						}
					}
				);
			}

			return this.$el;
		};

		$( document.body ).on(
			'ea_select2_init',
			function () {
				$( '.ea-select2' )
				.filter( ':not(.select2-hidden-accessible)' )
				.each(
					function () {
						$( this ).eaccounting_select2();
					}
				);
			}
		);

		$( '.ea-select2' ).eaccounting_select2();
	}
);
