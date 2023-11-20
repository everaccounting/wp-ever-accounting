const EAC = {
	vars: window.eac_admin_vars,
	cache: {},
	init: function () {
		this.bindEvents();
	},
	bindEvents: function () {
		// init select2
		jQuery(document)
			.on('init-select2', function () {
				EAC.fn.initSelect2('.eac-select2');
			})
			.on('init-tooltip', function () {
				EAC.fn.initTooltip('.eac-tooltip');
			});
	},
	fn: {
		initSelect2: function (selector, args) {},
		initDatepicker: function (selector, args) {},
		initTooltip: function (selector, args) {},
		notify: function (message, type) {},
		confirm: function (message, callback) {},
		confirmDelete: function (callback) {},
		redirect: function (url) {},
		getFormData: function (form) {},
		submitForm: function (form, callback) {},
		maskMoney: function (selector, args) {},
		convertAmount: function (amount, from, to) {},
	},
	api: {
		getCurrency: function (code, callback) {
			EAC.utils.ajax(
				{
					action: 'eac_get_currency',
					code: code,
				},
				function (response) {
					callback(response);
				}
			);
		},
		getCategory: function (id, callback) {},
		getCategories: function (args, callback) {},
		getAccount: function (id, callback) {},
		getAccounts: function (args, callback) {},
		getPayment: function (id, callback) {},
		getPayments: function (args, callback) {},
		getExpense: function (id, callback) {},
		getExpenses: function (args, callback) {},
		getInvoice: function (id, callback) {},
		getInvoices: function (args, callback) {},
		getCustomer: function (id, callback) {},
		getCustomers: function (args, callback) {},
		getVendor: function (id, callback) {},
		getVendors: function (args, callback) {},
		getTax: function (id, callback) {},
		getTaxes: function (args, callback) {},
		getItem: function (id, callback) {},
		getItems: function (args, callback) {},
		search: function (string, type, callback) {},
		getHTML: function (args, callback) {},
	},
	utils: {
		ajax: function (
			data,
			success = () => {},
			failed = () => {},
			always = () => {}
		) {
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				data: data,
				dataType: 'json',
				success: function (response) {
					if (response.success) {
						success(response.data);
					} else {
						failed(response.data);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					failed(errorThrown);
				},
				complete: function () {
					always();
				},
			});
		},
	},
};

// on document ready.
jQuery(document).ready(function ($) {
	EAC.init();

	// init select2
	$(document).trigger('init-select2');
	$(document).trigger('init-tooltip');
	$(document).trigger('init-datepicker');
	$(document).trigger('init-mask-money');
});
