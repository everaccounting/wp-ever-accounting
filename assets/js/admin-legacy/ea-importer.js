/* global eaccounting_importer_i10n */
jQuery( function ( $ ) {
	$.eaccounting_importer = function ( form, options ) {
		this.defaults = {};
		this.form = form;
		this.$form = $( form );
		this.$top = $( '.ea-importer-top', this.$form );
		this.$bottom = $( '.ea-importer-bottom', this.$form );
		this.options = $.extend( this.defaults, options );
		this.action = 'eaccounting_do_ajax_import';
		this.nonce = this.$form.data( 'nonce' );
		this.type = this.$form.data( 'type' );
		this.sample = {};
		this.mapping = {};
		this.file = '';
		const plugin = this;

		this.submit = function ( e ) {
			e.preventDefault();

			plugin.$form.find( '.ea-batch-notice' ).remove();
			//if disabled submit button then bail
			const $submit_btn = $( 'input[type="submit"]', plugin.$top );
			const $file_field = $( 'input[type="file"]', plugin.$top );
			if ( $submit_btn.hasClass( 'disabled' ) ) {
				return false;
			}

			if ( plugin.$form.hasClass( 'mapped' ) ) {
				plugin.mapping = plugin.$form.serializeAssoc().mapping;
				plugin.$form.append(
					'<div class="ea-batch-notice"><div class="ea-batch-progress"><div></div></div></div>'
				);
				plugin.$form
					.find( 'input[type="submit"]' )
					.attr( 'disabled', 'disabled' );
				plugin.$form
					.find( '.ea-importer-map-column' )
					.attr( 'disabled', 'disabled' );
				plugin.process_step( 0 );
				return false;
			}

			const data = new FormData();
			data.append( 'upload', $file_field[ 0 ].files[ 0 ] );
			data.append( 'action', plugin.action );
			data.append( 'nonce', plugin.nonce );
			data.append( 'type', plugin.type );
			data.append( 'step', 'upload' );

			//now disable button
			$submit_btn.addClass( 'disabled' );
			$file_field.attr( 'disabled', 'disabled' );
			$submit_btn
				.closest( 'p' )
				.append( '<span class="spinner is-active"></span>' );
			plugin.$form
				.find( '.ea-importer-map-column' )
				.attr( 'disabled', 'disabled' );

			window.wp.ajax.send( {
				type: 'POST',
				data,
				dataType: 'json',
				cache: false,
				contentType: false,
				processData: false,
				success( res ) {
					$submit_btn.find( '.spinner' ).remove();
					plugin.$form.addClass( 'mapped' );
					$submit_btn.removeClass( 'disabled' );
					plugin.sample = res.sample;
					plugin.file = res.file;
					plugin.$form
						.find( '.ea-importer-map-column' )
						.removeAttr( 'disabled' );
					plugin.$form.trigger( 'upload_complete', [ res ] );
				},
				error( error ) {
					plugin.$form.find( '.spinner' ).remove();
					$submit_btn.removeClass( 'disabled' );
					$file_field.removeAttr( 'disabled' );
					plugin.$form.append(
						'<div class="ea-batch-notice"><div class="updated error"><p>' +
							error.message +
							'</p></div></div>'
					);
				},
			} );

			return false;
		};

		this.process_step = function ( position ) {
			const $submit_btn = plugin.$form.find( 'input[type="submit"]' );
			window.wp.ajax.send( plugin.action, {
				data: {
					nonce: plugin.nonce,
					type: plugin.type,
					position,
					file: plugin.file,
					mapping: plugin.mapping,
				},
				success( res ) {
					if ( res.position === 'done' ) {
						$submit_btn.remove();
						plugin.$form.find( '.ea-batch-notice' ).remove();
						plugin.$form.append(
							'<div class="ea-batch-notice"><div class="updated success"><p>' +
								res.message +
								'</p></div></div>'
						);
						return false;
					}
					plugin.$form.find( '.ea-batch-progress div' ).animate(
						{
							width: res.percentage + '%',
						},
						50,
						function () {}
					);
					plugin.process_step( parseInt( res.position, 10 ) );
				},
				error( error ) {
					$submit_btn.removeAttr( 'disabled' );
					plugin.$form.find( '.ea-batch-notice' ).remove();
					plugin.$form
						.find( '.ea-importer-map-column' )
						.removeAttr( 'disabled' );
					if ( error.message ) {
						plugin.$form.append(
							'<div class="ea-batch-notice"><div class="updated error"><p>' +
								error.message +
								'</p></div></div>'
						);
					}
				},
			} );
		};

		this.init_mapping = function ( e, response ) {
			if ( $.isEmptyObject( response ) ) {
				plugin.$form.find( '.ea-batch-notice' ).remove();
				plugin.$form.append(
					'<div class="ea-batch-notice"><div class="updated error"><p>' +
						eaccounting_importer_i10n.uploaded_file_not_found +
						'</p></div></div>'
				);
				return false;
			}

			plugin.$top.hide();
			plugin.$bottom.slideDown();

			const select = $( '.ea-importer-map-column', plugin.$bottom );
			let options = [];

			$.each( select, function () {
				const currentSelect = $( this ),
					selectName = $( this ).attr( 'name' ),
					$tr = $( this ).closest( 'tr' );

				if (
					$.inArray(
						selectName
							.replace( 'mapping', '' )
							.replace( /[\[\]\]]/g, '' ),
						response.required
					) !== -1
				) {
					$tr.find( 'td' )
						.eq( 0 )
						.append(
							' <strong>' +
								eaccounting_importer_i10n.required +
								'</strong>'
						);
					currentSelect.attr( 'required', 'required' );
				}

				$.each( response.headers, function ( columnKey, columnValue ) {
					const processedColumnValue = columnValue
						.toLowerCase()
						.replace( / /g, '_' )
						.replace( /[""]/g, '' );
					const columnRegex = new RegExp(
						'\\[' + processedColumnValue + '\\]'
					);
					if (
						selectName.length &&
						selectName.match( columnRegex )
					) {
						// If the column matches a select, auto-map it. Boom.
						options +=
							'<option value="' +
							columnValue +
							'" selected="selected">' +
							columnValue +
							'</option>';
					} else {
						options +=
							'<option value="' +
							columnValue +
							'">' +
							columnValue +
							'</option>';
					}
				} );

				// Add the options markup to the select.
				$( this ).append( options ).trigger( 'change' );

				// Reset options.
				options = '';
			} );
		};

		this.handle_preview = function () {
			const index = $( this ).prop( 'selectedIndex' );
			if ( ! index ) {
				$( this )
					.parent()
					.next()
					.html( eaccounting_importer_i10n.select_field_to_preview );
			} else if ( plugin.sample[ index - 1 ] !== false ) {
				$( this )
					.parent()
					.next()
					.html( plugin.sample[ index - 1 ] );
			} else {
				$( this )
					.parent()
					.next()
					.html( eaccounting_importer_i10n.select_field_to_preview );
			}
		};

		/**
		 * Initialize the plugin.
		 */
		this.init = function () {
			this.$form
				.on( 'submit', this.submit )
				.on( 'upload_complete', plugin.$form, this.init_mapping )
				.on( 'change', '.ea-importer-map-column', this.handle_preview );
		};

		this.init();
		return this;
	};

	$.fn.eaccounting_importer = function ( options ) {
		return this.each( function () {
			new $.eaccounting_importer( this, options );
		} );
	};

	$( '.ea-importer' ).eaccounting_importer();
} );
