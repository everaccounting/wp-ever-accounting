jQuery( document ).ready( ( $ ) => {
	'use strict';

	var initializeUI = function () {
		// Select2.
		$( '.eac_select2' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const options = {
					allowClear:
						( $this.data( 'allow-clear' ) && ! $this.prop( 'multiple' ) ) || true,
					placeholder: $this.data( 'placeholder' ) || '',
					width: '100%',
					minimumInputLength: $this.data( 'minimum-input-length' ) || 0,
					readOnly: $this.data( 'readonly' ) || false,
					ajax: {
						url: eac_admin_vars.ajax_url,
						dataType: 'json',
						delay: 250,
						method: 'POST',
						data: function ( params ) {
							return {
								term: params.term,
								action: $this.data( 'action' ),
								type: $this.data( 'type' ),
								subtype: $this.data( 'subtype' ),
								_wpnonce: eac_admin_vars.search_nonce,
								exclude: $this.data( 'exclude' ),
								include: $this.data( 'include' ),
								limit: $this.data( 'limit' ),
							};
						},
						processResults: function ( data ) {
							data.page = data.page || 1;
							return data;
						},
						cache: true,
					},
				};
				// if data-action is not defined then return.
				if ( ! $this.data( 'action' ) ) {
					delete options.ajax;
				}
				$this.addClass( 'enhanced' ).selectWoo( options );
			} );

		// Datepicker.
		$( '.eac_datepicker' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const options = {
					dateFormat: $this.data( 'format' ) || 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					showOtherMonths: true,
					selectOtherMonths: true,
					yearRange: '-100:+10',
				};
				$this.addClass( 'enhanced' ).datepicker( options );
			} );

		// Tooltip.
		$( '.eac_tooltip' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const options = {
					position: {
						my: 'center bottom-15',
						at: 'center top',
					},
					tooltipClass: 'eac_tooltip',
				};
				$this.addClass( 'enhanced' ).tooltip( options );
			} );

		// currency.
		$( ':input.eac_amount' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const currency = $this.data( 'currency' ) || eac_admin_vars.base_currency;
				const precision = eac_admin_vars.currencies[ currency ].precision || 2;
				const symbol = eac_admin_vars.currencies[ currency ].symbol || '';
				const position = eac_admin_vars.currencies[ currency ].position || 'before';
				const thousand = eac_admin_vars.currencies[ currency ].thousand || ',';
				const decimal = eac_admin_vars.currencies[ currency ].decimal || '.';
				$this
					.inputmask( {
						alias: 'currency',
						placeholder: '0.00',
						rightAlign: false,
						allowMinus: true,
						digits: precision,
						radixPoint: decimal,
						groupSeparator: thousand,
						prefix: 'before' === position ? symbol : '',
						suffix: 'after' === position ? symbol : '',
						removeMaskOnSubmit: true,
					} )
					.addClass( 'enhanced' );
			} );

		// exchange rate.
		$( ':input.eac_exchange_rate' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const currency = $this.data( 'currency' ) || eac_admin_vars.base_currency;
				const symbol = eac_admin_vars.currencies[ currency ].symbol || '';
				$this
					.inputmask( {
						alias: 'currency',
						placeholder: '0.00',
						rightAlign: false,
						allowMinus: true,
						digits: 4,
						suffix: symbol,
						removeMaskOnSubmit: true,
					} )
					.addClass( 'enhanced' );
			} );

		// inputMask.
		$( '.eac_inputmask' )
			.filter( ':not(.enhanced)' )
			.each( function () {
				const $this = $( this );
				const options = {
					alias: $this.data( 'alias' ) || 'decimal',
					rightAlign: false,
					allowMinus: false,
					placeholder: $this.data( 'placeholder' ) || '',
					clearIncomplete: $this.data( 'clear-incomplete' ) || false,
					removeMaskOnSubmit: true,
				};
				$this.addClass( 'enhanced' ).inputmask( options );
			} );

		// Polyfill for card padding for firefox.
		$( '.eac-card' ).each( function () {
			if (
				! $( this ).children( '[class*="eac-card__"]' ).length &&
				! parseInt( $( this ).css( 'padding' ) )
			) {
				$( this ).css( 'padding', '8px 12px' );
			}
		} );

		// remove .eac-card__body if it is empty.
		$( '.eac-card__body' ).each( function () {
			// check for text or any html tags and not it can
			if ( ! $( this ).text().trim().length && $( this ).children().length === 0 ) {
				$( this ).remove();
			}
		} );
	};

	// Initialize UI.
	initializeUI();

	// Reinitialize UI when document body triggers 'eac-update-ui'.
	$( document.body ).on( 'eac_update_ui', initializeUI );

	// Media Uploader.
	$( '.eac-file-upload' )
		.filter( ':not(.enhanced)' )
		.each( function () {
			const $this = $( this );
			const $button = $this.find( '.eac-file-upload__button' );
			const $value = $this.find( '.eac-file-upload__value' );
			const $preview = $this.find( '.eac-file-upload__icon img' );
			const $name = $this.find( '.eac-file-upload__name a' );
			const $size = $this.find( '.eac-file-upload__size' );
			const $remove = $this.find( 'a.eac-file-upload__remove' );

			$button.on( 'click', function ( e ) {
				e.preventDefault();
				const frame = wp.media( {
					title: $button.data( 'uploader-title' ),
					multiple: false,
				} );
				frame.on( 'ready', function () {
					frame.uploader.options.uploader.params = {
						type: 'eac_file',
					};
				} );
				frame.on( 'select', function () {
					const attachment = frame.state().get( 'selection' ).first().toJSON();
					const src = attachment.type === 'image' ? attachment.url : attachment.icon;
					$value.val( attachment.id );
					$preview.attr( 'src', src ).show();
					$preview.attr( 'alt', attachment.filename );
					$name.text( attachment.filename ).attr( 'href', attachment.url );
					$size.text( attachment.filesizeHumanReadable );
					$remove.show();
					$this.addClass( 'has--file' );
				} );
				frame.open();
			} );

			$remove.on( 'click', function ( e ) {
				e.preventDefault();
				$this.removeClass( 'has--file' );
				$value.val( '' );
				$preview.attr( 'src', '' ).hide();
				$name.text( '' ).attr( 'href', '' );
				$size.text( '' );
			} );
		} );

	// Delete confirmation.
	$( '.del_confirm' ).on( 'click', function ( e ) {
		if ( ! confirm( eac_admin_vars.i18n.confirm_delete ) ) {
			e.preventDefault();
			return false;
		}
	} );

	// Notes.
	$( document.body )
		.on( 'keydown', '#eac-note', function ( e ) {
			if ( e.keyCode === 13 && ( e.metaKey || e.ctrlKey ) ) {
				e.preventDefault();
				$( '#eac-add-note' ).click();
			}
		} )
		.on( 'click', '#eac-add-note', function ( e ) {
			e.preventDefault();
			var $button = $( this );
			var $note = $( '#eac-note' );
			var $notes = $( '.eac-notes' );

			var data = {
				action: 'eac_add_note',
				nonce: $button.data( 'nonce' ),
				parent_id: $button.data( 'parent_id' ),
				parent_type: $button.data( 'parent_type' ),
				content: $note.val(),
			};

			if ( data.content ) {
				$button.prop( 'disabled', true );

				$.ajax( {
					type: 'POST',
					url: eac_admin_vars.ajax_url,
					data: data,
					success: function ( response ) {
						let res = wpAjax.parseAjaxResponse( response );
						res = res.responses[ 0 ];
						$notes.prepend( res.data );
						$note.val( '' );
						$button.prop( 'disabled', false );
						$notes.find( '.no-items' ).hide();
					},
				} ).fail( function () {
					$button.prop( 'disabled', false );
				} );
			}
		} )
		.on( 'click', '.eac-notes .note__delete', function ( e ) {
			e.preventDefault();
			var $button = $( this );
			var $note = $button.closest( '.note' );
			var $notes = $note.closest( '.eac-notes' );
			var data = {
				action: 'eac_delete_note',
				nonce: $button.data( 'nonce' ),
				note_id: $button.data( 'note_id' ),
			};

			if ( confirm( eac_admin_vars.i18n.confirm_delete ) ) {
				$.ajax( {
					type: 'POST',
					url: eac_admin_vars.ajax_url,
					data: data,
					success: function ( response ) {
						if ( '1' === response ) {
							console.log( response );
							$note.remove();
						}

						if ( $notes.find( 'li' ).length === 0 ) {
							$notes.find( '.no-items' ).show();
						}
					},
				} );
			}
		} );

	// print.
	$( '.eac_print_document' ).on( 'click', function ( e ) {
		e.preventDefault();
		var $target = $( $( this ).data( 'target' ) );
		console.log( $target );
		if ( ! $target.length ) {
			return;
		}

		$target.printThis( {
			printContainer: true,
			printDelay: 0,
			header: null,
			footer: null,
		} );
	} );

	// Share.
	$( '.eac_share_document' ).on( 'click', function ( e ) {
		e.preventDefault();
		var url = $( this ).data( 'url' );
		if ( ! url ) {
			return;
		}
		var $temp = $( '<input>' );
		$( 'body' ).append( $temp );
		$temp.val( url ).select();
		document.execCommand( 'copy' );
		$temp.remove();
		if ( window.prompt( eac_admin_vars.i18n.share_prompt, url ) ) {
			// open in new tab.
			window.open( url, '_blank' );
		}
	} );

	// Block UI
	$.fn.block = function ( destroy ) {
		return this.each( function () {
			var $el = $( this );
			if ( destroy && $el.find( '.blockUI' ).length ) {
				$el.find( '.blockUI' ).remove();
				return;
			}

			// If destroy is false, just create the overlay if it doesn't exist
			if ( ! $el.find( '.blockUI' ).length ) {
				// Ensure position is relative
				if ( $el.css( 'position' ) === 'static' ) {
					$el.css( 'position', 'relative' );
				}

				// Create overlay
				$( '<div class="blockUI"></div>' )
					.css( {
						position: 'absolute',
						top: 0,
						left: 0,
						width: '100%',
						height: '100%',
						backgroundColor: 'rgb(255, 255, 255)',
						opacity: 0.1,
						cursor: 'wait',
						zIndex: 9999,
					} )
					.appendTo( $el );
			}
		} );
	};
} );
