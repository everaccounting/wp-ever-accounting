jQuery( document ).ready( function ( $ ) {
	$( 'body' ).on( 'click', 'input#eac_business_logo', function ( e ) {
		e.preventDefault();
		var $this = $( this );

		const frame = wp.media( {
			multiple: false,
			// images only.
			library: { type: 'image' },
		} );

		frame.on( 'select', function () {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			$this.val( attachment.url );
		} );

		frame.open();
	} );

	$( '.ea-financial-start' ).datepicker( { dateFormat: 'dd-mm' } );

	$( '.eac-exchange-rates' )
		.on( 'click', 'a.add', function ( e ) {
			e.preventDefault();
			$( this )
				.closest( 'table' )
				.find( 'tbody' )
				.append( $( this ).data( 'row' ) )
		} )
		.on('change', 'select', function (e) {
			e.preventDefault();
			$( this )
				.closest( 'tr')
				.find( 'input, select' )
				.each( function () {
					var $this = $( this );
					$this.attr(
						'name',
						$this.attr( 'name' ).replace( /\[.*\]/, '[' + e.target.value + ']' )
					);
				} );
		} )
		.on( 'click', 'a.remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( 'tr' ).remove();
		} );
} );
