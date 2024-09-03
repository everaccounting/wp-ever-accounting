export const LineTax = Backbone.Model.extend({
	defaults: {
		'id': null,
		'name': '',
		'rate': 0,
		'is_compound': false,
		'amount': 0,
		'line_id': null,
		'tax_id': null,
		'document_id': null,
	}
});
