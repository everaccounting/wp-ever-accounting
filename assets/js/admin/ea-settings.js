jQuery( document ).ready( function ( $ ) {
	// Settings uploader
	let file_frame;
	window.formfield = '';

	$( 'body' ).on( 'click', '.ea_settings_upload_button', function ( e ) {
		e.preventDefault();

		const button = $( this );

		window.formfield = $( this ).parent().prev();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame
		file_frame = wp.media.frames.file_frame = wp.media( {
			frame: 'post',
			state: 'insert',
			title: button.data( 'uploader_title' ),
			button: {
				text: button.data( 'uploader_button_text' ),
			},
			multiple: false,
		} );

		file_frame.on( 'menu:render:default', function ( view ) {
			// Store our views in an object,
			const views = {};

			// Unset default menu items
			view.unset( 'library-separator' );
			view.unset( 'gallery' );
			view.unset( 'featured-image' );
			view.unset( 'embed' );

			// Initialize the views in our view object
			view.set( views );
		} );

		// When an image is selected, run a callback
		file_frame.on( 'insert', function () {
			const selection = file_frame.state().get( 'selection' );

			selection.each( function ( attachment, index ) {
				attachment = attachment.toJSON();
				window.formfield.val( attachment.url );
			} );
		} );

		// Open the modal
		file_frame.open();
	} );

	$( '.ea-financial-start' ).datepicker( 'destroy' );
	$( '.ea-financial-start' ).datepicker( { dateFormat: 'dd-mm' } );

	var currency_settings = {
		init() {
			$( '#ea-currency-settings' )
				.on(
					'change',
					'.ea_currencies_code select',
					this.populate_currency
				)
				.on( 'click', '#ea_add_currency', this.add_currency )
				.on( 'click', '.ea_remove_currency', this.remove_currency )
				.on( 'submit', this.submit );
		},
		block() {
			$( '#ea-currency-settings' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				},
			} );
			$( '#ea-currency-settings button' ).attr( 'disabled', 'disabled' );
		},

		unblock() {
			$( '#ea-currency-settings' ).unblock();
			$( '#ea-currency-settings button' ).removeAttr( 'disabled' );
		},
		add_currency( e ) {
			e.preventDefault();
			$( '.ea-select2' ).select2( 'destroy' );
			const row = $( '#ea_currencies tr:last' );
			const clone = row.clone();
			const count = row.parent().find( 'tr' ).length;
			clone.find( 'td input' ).not( ':input[type=checkbox]' ).val( '' );
			clone.find( 'td [type="checkbox"]' ).attr( 'checked', false );
			clone.find( 'input, select' ).each( function () {
				let name = $( this ).attr( 'name' );
				if ( name.includes( 'code' ) ) {
					$( this ).val( '' );
				}
				name = name.replace(
					/\[(\d+)\]/,
					'[' + parseInt( count ) + ']'
				);
				$( this ).attr( 'name', name ).attr( 'id', name );
			} );
			clone.insertAfter( row );
			$( document.body ).trigger( 'ea_select2_init' );
			return false;
		},
		remove_currency( e ) {
			e.preventDefault();
			const count = $( '#ea_currencies tbody tr' ).length;
			if ( count < 2 ) {
				$.eaccounting_notice( {
					message: 'Last item is not removable.',
				} );
				return false;
			}
			$( this ).closest( 'tr' ).remove();
		},
		populate_currency() {
			const code = $( this ).val();
			if ( ! code ) {
				return;
			}
			const currency = eaccounting_codes[ code ];
			$( this )
				.closest( 'tr' )
				.find( '.ea_currencies_rate input' )
				.val( '1.0000' )
				.end()
				.find( '.ea_currencies_precision input' )
				.val( currency.precision )
				.end()
				.find( '.ea_currencies_position select' )
				.val( currency.position )
				.end()
				.find( '.ea_currencies_decimal_separator input' )
				.val( currency.decimal_separator )
				.end()
				.find( '.ea_currencies_thousand_separator input' )
				.val( currency.thousand_separator )
				.end();
		},
		submit( e ) {
			e.preventDefault();
			currency_settings.block();
			const data = $( '#ea-currency-settings' ).serializeObject();
			$.post( ajaxurl, data, function ( json ) {} ).always( function (
				json
			) {
				$.eaccounting_notice( json );
				currency_settings.unblock();
			} );
		},
	};

	currency_settings.init();
} );
