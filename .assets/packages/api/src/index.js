/**
 * Base API model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Model = Backbone.Model.extend({
	/**
	 * API root.
	 */
	apiRoot: wpApiSettings.root || '/wp-json',

	/**
	 * API namespace.
	 */
	namespace: 'eac/v1',

	/**
	 * API endpoint.
	 */
	endpoint: '',

	/**
	 * Request nonce.
	 */
	nonce: wpApiSettings.nonce || '',

	/**
	 * Get the URL for the model.
	 *
	 * @return {string}.
	 */
	url: function () {
		var url = this.apiRoot.replace(/\/+$/, '') + '/' + this.namespace.replace(/\/+$/, '') + '/' + this.endpoint.replace(/\/+$/, '');
		if (!_.isUndefined(this.get('id'))) {
			url += '/' + this.get('id');
		}
		return url;
	},

	/**
	 * Set nonce header before every Backbone sync.
	 *
	 * @param {string} method.
	 * @param {Backbone.Model} model.
	 * @param {{beforeSend}, *} options.
	 * @return {*}.
	 */
	sync: function (method, model, options) {
		var beforeSend;
		options = options || {};
		// if cached is not set then set it to true.
		if (_.isUndefined(options.cache)) {
			options.cache = true;
		}

		// Include the nonce with requests.
		if (!_.isEmpty(model.nonce)) {
			beforeSend = options.beforeSend;
			options.beforeSend = function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', model.nonce);

				if (beforeSend) {
					return beforeSend.apply(this, arguments);
				}
			};

			// Update the nonce when a new nonce is returned with the response.
			options.complete = function (xhr) {
				var returnedNonce = xhr.getResponseHeader('X-WP-Nonce');

				if (returnedNonce && _.isEmpty(model.nonce) && model.nonce !== returnedNonce) {
					model.set('nonce', returnedNonce);
				}
			};
		}

		return Backbone.sync(method, model, options);
	},
});

/**
 * Account model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Account = Model.extend({
	endpoint: 'accounts',

	defaults: {
		id: '',
		type: 'bank',
		name: '',
		number: '',
		opening_balance: 0,
		bank_name: '',
		bank_phone: '',
		bank_address: '',
		currency_code: 'USD',
		creator_id: '',
		thumbnail_id: '',
		status: 'active',
		updated_at: '',
		created_at: '',
	},
});

/**
 * Contact model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Contact = Model.extend({
	endpoint: 'contacts',

	defaults: {
		id: null,
		type: 'customer',
		name: '',
		company: '',
		email: '',
		phone: '',
		website: '',
		address: '',
		city: '',
		state: '',
		postcode: '',
		country: '',
		vat_number: '',
		vat_exempt: false,
		currency_code: '',
		thumbnail_id: null,
		user_id: null,
		status: 'active',
		created_via: 'api',
		creator_id: null,
		updated_at: '',
		created_at: '',
	},
});

/**
 * Customer model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Customer = Contact.extend({
	endpoint: 'customers',

	defaults: Object.assign({}, Contact.prototype.defaults, {
		type: 'customer',
	}),
});

/**
 * Category model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Category = Model.extend({
	endpoint: 'categories',

	defaults: {
		id: null,
		type: '',
		name: '',
		description: '',
		status: '',
		updated_at: '',
		created_at: '',
	},

});

/**
 * Document model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Document =Model.extend({
	defaults: {
		id: '',
		type: '',
		status: 'draft',
		number: '',
		contact_id: '',
		subtotal: 0,
		discount: 0,
		tax_total: 0,
		total: 0,
		discount_value: 0,
		discount_type: 'fixed',

		reference: '',
		note: '',
		issue_date: '',
		due_date: '',
		sent_date: '',
		payment_date: '',
		currency_code: '',
		exchange_rate: '',
		parent_id: '',
		created_via: '',
		creator_id: '',
		uuid: '',
		updated_at: '',
		created_at: '',

		// Relationships
		address: {},
		currency: {},
		items: [],

		// Flags
		is_fetching: false,
	},

	getNextNumber(options = {}) {
		return this.fetch({
			url: `${this.apiRoot}${this.namespace}/utilities/next-number?type=${this.get('type')}`,
			type: 'GET',
			...options,
		})
	},


	/**
	 * Update Amount
	 *
	 * @return {void}
	 */
	updateAmounts() {
		console.log('=== Bill.updateAmounts() ===');
		var items_total = 0,
			subtotal = 0,
			discount = 0,
			tax_total = 0,
			total = 0,
			discount_type = this.get('discount_type') || 'fixed',
			discount_value = parseFloat(this.get('discount_value'), 10) || 0;

		console.log('discount_type:', discount_type);
		console.log('discount_value:', discount_value);

		// Prepare items for calculation
		_.each(this.get('items').models, (item) => {
			const _price = parseFloat(item.get('price'), 10) || 0;
			const _quantity = parseFloat(item.get('quantity'), 10) || 0;
			const _subtotal = _price * _quantity;
			const _type = item.get('type') || 'standard';
			// if standard, add to items_total.
			if (_type === 'standard') {
				items_total += _subtotal;
			}

			item.set({
				price: _price,
				quantity: _quantity,
				subtotal: _subtotal,
				type: _type,
				discount: 0,
			});
		});

		discount = discount_type === 'percent' ? (items_total * discount_value) / 100 : discount_value;
		discount = discount > items_total ? items_total : discount;


		_.each(this.get('items').models, (item) => {
			var _type = item.get('type') || 'standard',
				_subtotal = item.get('subtotal') || 0,
				_discount = _type === 'standard' ? (discount / items_total) * _subtotal : 0,
				_disc_subtotal = Math.max(_subtotal - _discount, 0);

			console.log('type:', _type);
			console.log('discount:', _discount);

			// Simple tax calculation.
			_.each(item.get('taxes').models, (tax) => {
				const _tax_rate = parseFloat(tax.get('rate'), 10) || 0;
				const _tax_amount = !tax.get('compound') ? _disc_subtotal * (_tax_rate / 100) : 0;
				tax.set({
					rate: _tax_rate,
					amount: _tax_amount,
				});
			});

			const _prev_tax = _.reduce(item.get('taxes').models, (sum, tax) => {
				return sum + tax.get('amount')
			}, 0);

			_.each(item.get('taxes').where({compound: true}), (tax) => {
				const _tax_rate = parseFloat(tax.get('rate'), 10) || 0;
				const _tax_amount = (_disc_subtotal + _prev_tax) * (_tax_rate / 100);
				tax.set({
					rate: _tax_rate,
					amount: _tax_amount,
				});
			});

			const _tax_total = _.reduce(item.get('taxes').models, (sum, tax) => {
				return sum + tax.get('amount')
			}, 0);

			item.set({
				discount: _discount,
				subtotal: _disc_subtotal,
				tax_total: _tax_total,
				total: _disc_subtotal + _tax_total,
			});

			subtotal += _disc_subtotal;
			tax_total += _tax_total;
		});


		total = subtotal + tax_total;
		this.set({
			subtotal: subtotal,
			discount: discount,
			tax_total: tax_total,
			total: total,
		});
	},
});

/**
 * DocumentItem model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentItem = Model.extend({
	endpoint: 'line-items',

	defaults: {
		id: null,
		type: 'standard',
		name: '',
		price: 0,
		quantity: 1,
		subtotal: 0,
		discount: 0,
		tax_total: 0,
		total: 0,
		description: '',
		unit: '',
		item_id: null,
		updated_at: '',
		created_at: '',

		// Relationships
		taxes: []
	},
});

/**
 * DocumentTax model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentTax = Model.extend({
	endpoint: 'line-taxes',

	defaults: {
		id: null,
		name: '',
		rate: 0,
		compound: false,
		amount: 0,
		item_id: 0,
		tax_id: 0,
		document_id: 0,
		updated_at: '',
		created_at: '',
	},
});

/**
 * DocumentAddress model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentAddress = Model.extend({
	endpoint: 'addresses',

	defaults: {
		id: '',
		document_id: '',
		type: 'invoice',
		name: '',
		company: '',
		street: '',
		city: '',
		state: '',
		zip: '',
		country: '',
		phone: '',
		email: '',
		tax_number: '',
		updated_at: '',
		created_at: '',
	},
});


/**
 * Invoice model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Invoice = Document.extend({
	defaults: Object.assign({},  Document.prototype.defaults, {
		type: 'invoice',
	}),
});

/**
 * Bill model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Bill = Document.extend({
	defaults: Object.assign({}, Document.prototype.defaults, {
		type: 'bill',
	}),
});

/**
 * Transaction model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Transaction = Model.extend({
	endpoint: 'transactions',

	defaults: {
		id: '',
		type: 'expense',
		status: 'draft',
		number: '',
		contact_id: '',
		account_id: '',
		amount: 0,
		currency_code: '',
		exchange_rate: '',
		reference: '',
		note: '',
		date: '',
		created_via: '',
		creator_id: '',
		uuid: '',
		updated_at: '',
		created_at: '',
	},
});

/**
 * Payment model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Payment = Transaction.extend({
	defaults: Object.assign({}, Transaction.prototype.defaults, {
		type: 'payment',
	}),
});

/**
 * Expense model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Expense = Transaction.extend({
	defaults: Object.assign({}, Transaction.prototype.defaults, {
		type: 'expense',
	}),
});

/**
 * Item model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Item = Model.extend({
	endpoint: 'items',

	defaults: {
		id: '',
		name: '',
		description: '',
		price: 0,
		unit: '',
		updated_at: '',
		created_at: '',
	},
});

/**
 * Tax model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Tax = Model.extend({
	endpoint: 'taxes',

	defaults: {
		id: '',
		name: '',
		rate: 0,
		compound: false,
		updated_at: '',
		created_at: '',
	},
});

/**
 * Note model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Note = Model.extend({
	endpoint: 'notes',

	defaults: {
		id: '',
		type: '',
		object_id: '',
		content: '',
		updated_at: '',
		created_at: '',
	},
});

/**
 * Vendor model
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Vendor = Contact.extend({
	endpoint: 'vendors',

	defaults: Object.assign({}, Contact.prototype.defaults, {
		type: 'vendor',
	}),
});

/**
 * Base API collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Collection = Backbone.Collection.extend({
	/**
	 * API root.
	 */
	apiRoot: wpApiSettings.root || '/wp-json',

	/**
	 * API namespace.
	 */
	namespace: 'eac/v1',

	/**
	 * API endpoint.
	 */
	endpoint: '',

	/**
	 * Request nonce.
	 */
	nonce: wpApiSettings.nonce || '',

	/**
	 * Setup default state.
	 */
	preinitialize: function( models, options ) {
		this.options = options;
	},

	/**
	 * Get the URL for the model.
	 *
	 * @return {string}.
	 */
	url: function () {
		return this.apiRoot.replace(/\/+$/, '') + '/' + this.namespace.replace(/\/+$/, '') + '/' + this.endpoint.replace(/\/+$/, '');
	},

	/**
	 * Set nonce header before every Backbone sync.
	 *
	 * @param {string} method.
	 * @param {Backbone.Model} model.
	 * @param {{beforeSend}, *} options.
	 * @return {*}.
	 */
	sync: function (method, model, options) {
		var beforeSend;
		options = options || {};
		// if cached is not set then set it to true.
		if ( _.isUndefined( options.cache ) ) {
			options.cache = true;
		}

		// Include the nonce with requests.
		if ( ! _.isEmpty(model.nonce) ) {
			beforeSend = options.beforeSend;
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', model.nonce );

				if ( beforeSend ) {
					return beforeSend.apply( this, arguments );
				}
			};

			// Update the nonce when a new nonce is returned with the response.
			options.complete = function( xhr ) {
				var returnedNonce = xhr.getResponseHeader( 'X-WP-Nonce' );
				if ( ! _.isEmpty( returnedNonce ) ) {
					model.nonce = returnedNonce;
				}
			};
		}

		return Backbone.sync( method, model, options );
	}
});

/**
 * Account collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Accounts = Collection.extend({
	endpoint: 'accounts',
	model: Account,
});

/**
 * Contact collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Contacts = Collection.extend({
	endpoint: 'contacts',
	model: Contact,
});

/**
 * Customer collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Customers = Collection.extend({
	endpoint: 'customers',
	model: Customer,
});

/**
 * Category collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Categories = Collection.extend({
	endpoint: 'categories',
	model: Category,
});

/**
 * Document collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Documents = Collection.extend({
	endpoint: 'documents',
	model: Document,
});

/**
 * DocumentItem collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentItems = Collection.extend({
	endpoint: 'document/items',
	model: DocumentItem,
});

/**
 * DocumentTax collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentTaxes = Collection.extend({
	endpoint: 'line-taxes',
	model: DocumentTax,
});

/**
 * DocumentAddress collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const DocumentAddresses = Collection.extend({
	endpoint: 'addresses',
	model: DocumentAddress,
});

/**
 * Invoice collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Invoices = Collection.extend({
	endpoint: 'invoices',
	model: Invoice,
});

/**
 * Bill collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Bills = Collection.extend({
	endpoint: 'bills',
	model: Bill,
});

/**
 * Transaction collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Transactions = Collection.extend({
	endpoint: 'transactions',
	model: Transaction,
});

/**
 * Payment collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Payments = Collection.extend({
	endpoint: 'payments',
	model: Payment,
});

/**
 * Expense collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Expenses = Collection.extend({
	endpoint: 'expenses',
	model: Expense,
});

/**
 * Item collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Items = Collection.extend({
	endpoint: 'items',
	model: Item,
});

/**
 * Tax collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Taxes = Collection.extend({
	endpoint: 'taxes',
	model: Tax,
});

/**
 * Note collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Notes = Collection.extend({
	endpoint: 'notes',
	model: Note,
});

/**
 * Vendor collection
 *
 * @type {Object}
 * @since 1.0.0
 */
export const Vendors = Collection.extend({
	endpoint: 'vendors',
	model: Vendor,
});
