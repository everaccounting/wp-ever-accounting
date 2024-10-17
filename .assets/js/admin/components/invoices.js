jQuery( document ).ready( ( $ ) => {
	'use strict';


	/**
	 * Invoice Form.
	 *
	 * @constructor
	 */
	function Invoice_Form( el ) {
		var form = this;

		/**
		 * Recipient view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.BillingAddr = wp.Backbone.View.extend( {
			el: '.billing-address',

			template: wp.template( 'eac-invoice-billing-addr' ),

			initialize() {
				const { state } = this.options;
				this.listenTo( state, 'change:contact_id', this.render );
			},

			prepare() {
				const { state } = this.options;

				return {
					...state.toJSON(),
				};
			},
		} );

		/**
		 * Invoice item view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Item = wp.Backbone.View.extend( {
			tagName: 'tr',

			className: 'eac-document-items__item',

			template: wp.template( 'eac-invoice-item' ),

			events: {
				'change .item-quantity': 'onQuantityChange',
				'change .item-price': 'onPriceChange',
				'select2:select .item-taxes': 'onAddTax',
				'select2:unselect .item-taxes': 'onRemoveTax',
				'click .remove-item': 'onRemoveLineItem',
			},

			initialize() {
				const { state } = this.options;
				this.listenTo( this.model, 'change', this.render );
				this.listenTo( this.model, 'change', this.render );
				this.listenTo( this.model.get( 'taxes' ), 'add remove change', this.render );
				this.listenTo( state, 'change:currency', this.render );
			},

			prepare() {
				const { model, state } = this.options;
				const data = model.toJSON();
				return {
					...data,
					formatted_subtotal: state.get( 'money' ).format( data.subtotal ),
					formatted_tax: state.get( 'money' ).format( data.tax ),
					tax: model
						.get( 'taxes' )
						.reduce( ( acc, tax ) => acc + tax.get( 'amount' ), 0 ),
					taxes: data.taxes?.toJSON(),
				};
			},

			render() {
				console.log( '=== Invoice.Item.render() ===' );
				wp.Backbone.View.prototype.render.apply( this, arguments );
				$( document.body ).trigger( 'eac_update_ui' );
				return this;
			},

			onQuantityChange( e ) {
				e.preventDefault();
				var value = parseFloat( e.target.value, 10 );
				if ( ! value ) {
					this.onRemoveLineItem( e );
					return;
				}
				this.model.set( 'quantity', value );
				this.options.state.updateAmounts();
			},

			onPriceChange( e ) {
				e.preventDefault();
				var value = parseFloat( e.target.value, 10 );
				this.model.set( 'price', value );
				this.options.state.updateAmounts();
			},

			onAddTax( e ) {
				e.preventDefault();
				var data = e.params.data;
				var tax_id = parseInt( data.id, 10 ) || null;
				if ( tax_id ) {
					// any of the taxes already exists with the same tax_id then skip.
					if ( this.model.get( 'taxes' ).findWhere( { tax_id } ) ) {
						return;
					}

					var tax = new eac_api.Tax( { id: tax_id } );
					tax.fetch( {
						success: ( model ) => {
							this.model.get( 'taxes' ).add( {
								...model.toJSON(),
								tax_id: model.get( 'id' ),
								id: _.uniqueId( 'tax_' ),
							} );
							this.options.state.updateAmounts();
						},
					} );
				}
			},

			onRemoveTax( e ) {
				e.preventDefault();
				var data = e.params.data;
				var tax_id = parseInt( data.id, 10 ) || null;
				if ( tax_id ) {
					var tax = this.model.get( 'taxes' ).findWhere( { tax_id: tax_id } );
					if ( tax ) {
						this.model.get( 'taxes' ).remove( tax );
						this.options.state.updateAmounts();
					}
				}
			},

			onRemoveLineItem( e ) {
				e.preventDefault();
				this.options.state.get( 'items' ).remove( this.model );
				this.options.state.updateAmounts();
			},
		} );

		/**
		 * Invoice No Items view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.NoItems = wp.Backbone.View.extend( {
			tagName: 'tr',

			className: 'eac-document-items__no-items',

			template: wp.template( 'eac-invoice-empty' ),
		} );

		/**
		 * Invoice Items view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Items = wp.Backbone.View.extend( {
			tagName: 'tbody',

			className: 'eac-document-items__items',

			initialize() {
				const { state } = this.options;
				this.listenTo( state.get( 'items' ), 'add', this.render );
				this.listenTo( state.get( 'items' ), 'remove', this.render );
				this.listenTo( state.get( 'items' ), 'add', this.scrollToBottom );
			},

			render() {
				this.views.detach();
				const { state } = this.options;
				const items = state.get( 'items' );
				if ( ! items.length ) {
					this.views.add( new form.NoItems( this.options ) );
				} else {
					items.each( ( model ) => {
						this.views.add( new form.Item( { ...this.options, model } ) );
					} );
				}
				$( document.body ).trigger( 'eac_update_ui' );
				return this;
			},

			scrollToBottom() {
				var $el = this.$el.closest( 'tbody' ).find( 'tr:last-child' );
				$el.find( '.item-price' ).focus();
				// Now we need to scroll to the bottom of the table.
				var $table = this.$el.closest( 'table' );
				$( 'html, body' ).animate(
					{
						scrollTop: $el.offset().top - $table.offset().top + $table.scrollTop(),
					},
					500
				);
			},
		} );

		/**
		 * Invoice Toolbar view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Toolbar = wp.Backbone.View.extend( {
			tagName: 'tbody',

			className: 'eac-document-items__toolbar',

			template: wp.template( 'eac-invoice-toolbar' ),

			events: {
				'select2:select .add-item': 'onAddItem',
			},

			prepare() {
				const { state } = this.options;
				return {
					...state.toJSON(),
				};
			},

			onAddItem( e ) {
				e.preventDefault();
				const { state } = this.options;
				const item_id = parseInt( e.params.data.id, 10 ) || null;
				if ( item_id ) {
					$( e.target ).val( null ).trigger( 'change' );
					new eac_api.Item( { id: item_id } ).fetch().then( ( json ) => {
						const taxes = json.taxes || [];
						json.taxes = new eac_api.DocumentTaxes();
						json.taxes.add(
							taxes.map( ( tax ) => ( {
								...tax,
								id: _.uniqueId( 'tax_' ),
								rate: tax.rate,
								tax_id: tax.id,
								amount: 0,
							} ) )
						);
						state.get( 'items' ).add( {
							...json,
							id: _.uniqueId( 'item_' ),
							price: ( json.price || 0 ) * state.get( 'exchange_rate' ),
							quantity: 1,
							item_id: json.id,
						} );
						state.updateAmounts();
					} );
				}
			},
		} );

		/**
		 * Invoice Totals view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Totals = wp.Backbone.View.extend( {
			tagName: 'tfoot',

			className: 'eac-document-items__totals',

			template: wp.template( 'eac-invoice-totals' ),

			events: {
				'change [name="discount_value"]': 'onDiscountValueChange',
				'change [name="discount_type"]': 'onDiscountTypeChange',
			},

			initialize() {
				const { state } = this.options;
				this.listenTo( state, 'change:currency', this.render );
				this.listenTo( state, 'change', this.render );
			},

			prepare() {
				const { state } = this.options;
				return {
					...state.toJSON(),
					formatted_subtotal: state.get( 'money' ).format( state.get( 'subtotal' ) ),
					formatted_discount: state.get( 'money' ).format( state.get( 'discount' ) ),
					formatted_tax: state.get( 'money' ).format( state.get( 'tax' ) ),
					formatted_total: state.get( 'money' ).format( state.get( 'total' ) ),
				};
			},
			onDiscountValueChange( e ) {
				e.preventDefault();
				var state = this.options.state;
				var value = parseFloat( e.target.value, 10 );
				state.set( 'discount_value', value );
				state.updateAmounts();
			},

			onDiscountTypeChange( e ) {
				var state = this.options.state;
				var value = e.target.value;
				state.set( 'discount_type', value );
				state.updateAmounts();
			},
		} );

		/**
		 * Invoice Main view.
		 *
		 * @type {wp.Backbone.View}
		 * @since 1.0.0
		 */
		this.Main = wp.Backbone.View.extend( {
			el: el,

			events: {
				'change [name="contact_id"]': 'onChangeContact',
				'change :input[name="currency"]': 'onChangeCurrency',
				'change :input[name="exchange_rate"]': 'onChangeExchangeRate',
				'select2:select .add-item': 'onAddItem',
			},

			render: function () {
				this.views.detach();
				this.views.add( '.billing-address', new form.BillingAddr( this.options ) );
				this.views.add( 'table.eac-document-items', new form.Items( this.options ) );
				this.views.add( 'table.eac-document-items', new form.Toolbar( this.options ) );
				this.views.add( 'table.eac-document-items', new form.Totals( this.options ) );
				$( document.body ).trigger( 'eac_update_ui' );
				return this;
			},
			BlockUnblockUI: function () {
				if ( ! this.$el.find( '.blockUI' ).length ) {
					// Ensure position is relative
					if ( this.$el.css( 'position' ) === 'static' ) {
						this.$el.css( 'position', 'relative' );
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
						.appendTo( this.$el );
				} else {
					this.$el.find( '.blockUI' ).remove();
				}
			},
			onChangeContact: function ( e ) {
				e?.preventDefault();
				const { state } = this.options;
				const json = $( e.target ).select2( 'data' )?.[ 0 ];
				const dataToSet = Object.keys( state.toJSON() )
					.filter( ( key ) => key.startsWith( 'contact_' ) )
					.reduce( ( acc, key ) => {
						acc[ key ] =
							json && json.hasOwnProperty( key.slice( 8 ) )
								? json[ key.slice( 8 ) ]
								: '';
						return acc;
					}, {} );

				state.set( dataToSet );
			},
			onChangeCurrency( e ) {
				e?.preventDefault();
				var self = this;
				var $exchange = this.$( ':input[name="exchange_rate"]' );
				var currency = this.$( ':input[name="currency"]' ).val();
				self.options.state.set( 'money', new Money( currency ) );
				if ( currency ) {
					self.options.state.set( 'currency', currency );
					$exchange.val( eac_currencies[ currency ].rate || 1.0 ).trigger( 'change' );
					$exchange.attr( 'readonly', currency === eac_base_currency );
				}
			},
			onChangeExchangeRate( e ) {
				e?.preventDefault();
				var self = this;
				var $exchange = this.$( ':input[name="exchange_rate"]' );
				var rate = parseFloat( $exchange.val(), 10 );
				if ( rate ) {
					self.options.state.set( 'exchange_rate', rate );
				}
			},
		} );

		/**
		 * Invoice state model.
		 *
		 * @type {Backbone.Model}
		 * @since 1.0.0
		 */
		this.State = eac_api.Invoice.extend( {} );

		/**
		 * Initialize Invoice.
		 *
		 * @since 1.0.0
		 * @return {void}
		 */
		this.Init = function () {
			const currency = eac_invoice_vars?.currency || eac_base_currency;
			// create new invoice state.
			var state = new this.State( {
				...( window.eac_invoice_vars || {} ),
				money: new Money( currency ),
			} );
			state.set( 'items', new eac_api.DocumentItems() );

			// Hydrate collections.
			var items = eac_invoice_vars?.items || [];
			items.forEach( function ( _item ) {
				var taxes = _item.taxes || [];
				var item = new eac_api.DocumentItem( _item );
				item.set( 'taxes', new eac_api.DocumentTaxes() );
				taxes.forEach( function ( tax ) {
					item.get( 'taxes' ).add( tax );
				} );
				state.get( 'items' ).add( item );
			} );
			state.updateAmounts();
			return new this.Main( { state } ).render();
		};

		if ( $( el ).length ) {
			this.Init();
		}

		return this;
	}

	// Initialize Invoice.
	new Invoice_Form( '#eac-edit-invoice' );

	$('.add-invoice-payment').on('click', function (e) {
		e.preventDefault();
		$(this).eacmodal({
			template: 'eac-invoice-payment',
			events: {
				'change :input[name="account_id"]': 'onChangeAccount',
				'submit': 'onSubmit',
			},
			onChangeAccount: function (e) {
				const json = $( e.target ).select2( 'data' )?.[ 0 ];
				const $amount = this.$(':input[name="amount"]');
				const $exchange = this.$(':input[name="exchange_rate"]');
				const $account = this.$(':input[name="account_id"]');
				const account_id = $account.val();

				if (!account_id) {
					$exchange.val(1.0);
					$exchange.attr('readonly', true).val(1.0);
					return;
				}
				console.log(eac_currencies[json.currency]);
				$amount.data('currency', json.currency).removeClass('enhanced');
				$exchange.val(eac_currencies[json.currency].rate || 1.0);
				$exchange.attr('readonly', json.currency === eac_base_currency);
				$(document.body).trigger('eac_update_ui');
			},
			onSubmit: function (e) {
				const self = this ;
				e.preventDefault();
				var Payment = new eac_api.Payment(self.values())
				Payment.set('account', { id: Payment.get('account_id') });
				Payment.set('invoice', { id: Payment.get('invoice_id') });
				Payment.save(null, {
					method: 'POST',
					success: function (model) {
						console.log(model);
					}
				});
			},
		});
	});

});
