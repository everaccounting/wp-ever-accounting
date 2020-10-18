
/**
 * Color field wrapper for Ever Accounting
 * @since 1.0.2
 */
jQuery( function ( $ ) {
	jQuery.fn.ea_color_picker = function () {
		return this.each( function () {
			var el = this;
			$( el )
				.iris( {
					change: function ( event, ui ) {
						$( el )
							.parent()
							.find( '.colorpickpreview' )
							.css( { backgroundColor: ui.color.toString() } );
					},
					hide: true,
					border: true,
				} )
				.on( 'click focus', function ( event ) {
					event.stopPropagation();
					$( '.iris-picker' ).hide();
					$( el ).closest( 'div' ).find( '.iris-picker' ).show();
					$( el ).data( 'original-value', $( el ).val() );
				} )
				.on( 'change', function () {
					if ( $( el ).is( '.iris-error' ) ) {
						var original_value = $( this ).data( 'original-value' );

						if (
							original_value.match(
								/^\#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/
							)
						) {
							$( el )
								.val( $( el ).data( 'original-value' ) )
								.change();
						} else {
							$( el ).val( '' ).change();
						}
					}
				} );

			$( 'body' ).on( 'click', function () {
				$( '.iris-picker' ).hide();
			} );
		} );
	};

	$( '.ea-input-color' ).ea_color_picker();
} );
