import State from "./models/state";
import NoLineItems from "./views/no-items";
import Totals from "./views/totals";
import Actions from "./views/actions";
import LineItems from "./collections/line-items";

jQuery(document).ready(($)=>{


	var Form = wp.Backbone.View.extend({
		el: '#eac-invoice-form',

		initialize: function () {
			wp.Backbone.View.prototype.initialize.apply(this, arguments);
			this.listenTo(this.options.state, 'change', this.render);
		},

		render: function () {
			const {state} = this.options;
			const items = state.get('items');
			console.log(items);
			if (!items.length) {
				this.views.add('.eac-invoice-table', new NoLineItems(this.options));
			}
			this.views.add('.eac-invoice-table', new Totals(this.options));
			this.views.add('.eac-invoice-table', new Actions(this.options));
			return this;
		},
	});


	/**
	 * Initialize the invoice UI.
	 */
	var int = function () {
		const state = new State({
			...eac_invoices_vars.invoice || {},
		});

		state.set({
			items: new LineItems(null, {state}),
		});

		var form = new Form({state});
		form.render();
	};

	// Initialize the invoice UI.
	$(int);
});
