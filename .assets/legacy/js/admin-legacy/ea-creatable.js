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

	const defaults = {
		option( item ) {
			return { id: item.id, text: item.name };
		},
		template: undefined,
		onReady: undefined,
		onSubmit: undefined,
	};

	$.eaccounting_creatable = function ( el, options ) {
		this.el = el;
		this.$el = $( el );
		this.options = $.extend( {}, defaults, options );
		const plugin = this;

		this.handleSubmit = function ( $modal ) {
			const data = eaccounting.get_values( $( 'form', $modal.$modal ) );
			if ( typeof plugin.options.onSubmit === 'function' ) {
				return plugin.options.onSubmit( plugin.$el, data, $modal );
			}

			$.post( ajaxurl, data ).always( function ( json ) {
				$modal.unblock();

				if ( json.success ) {
					const option = plugin.options.option( json.data.item );
					plugin.$el.eaccounting_select2( { data: [ option ] } );
					plugin.$el.val( option.id ).trigger( 'change' );
					$modal.close();
				}

				eaccounting.notice( json );
			} );
		};
		this.handleModal = function ( e, $el, template ) {
			e.preventDefault();
			if ( $el.is( plugin.$el ) ) {
				$( template ).ea_modal( {
					onSubmit: plugin.handleSubmit,
					onReady( $modal ) {
						if ( typeof plugin.options.onReady === 'function' ) {
							plugin.options.onReady(
								plugin.$el,
								$modal,
								plugin
							);
						}
					},
				} );
			}
		};

		this.init = function () {
			$( document ).on( 'ea_trigger_creatable', plugin.handleModal );
		};

		this.init();

		return this;
	};

	$( '#account_id,#customer_id,#vendor_id' ).eaccounting_creatable();

	//creatable form
	// $('#currency_code').eaccounting_creatable({
	// 	option: function (item) {
	// 		return {
	// 			id: item.code,
	// 			text: item.name + ' (' + item.symbol + ')',
	// 		};
	// 	},
	// });

	// $('#category_id').eaccounting_creatable({
	// 	onReady: function ($el, $modal) {
	// 		var type = $el.data('type');
	// 		if (!type) {
	// 			console.warn('No category type defined');
	// 		}
	// 		$('#type', $modal.$el).val(type.replace('_category', ''));
	// 	},
	// });
} );
