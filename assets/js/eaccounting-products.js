document.addEventListener('DOMContentLoaded', function (event) {
	var eAccountingProduct = {
		form: jQuery('form#ea-product-form'),
		submit: jQuery('input[type="submit"]'),
		bindSubmit: function () {
			var that = this;
			if (this.form) {
				this.form.on('submit', this.handleFormSubmit);
			}
		},
		handleFormSubmit: function (e) {
			e.preventDefault();
			e.returnValue = false;
			eAccountingProduct.disableForm();
			var formData = eAccountingProduct.form.serializeArray();
			console.log(formData);

			wp.ajax.send('eaccounting_validate_insert_product',  {
				data: eAccountingProduct.form.serializeArray(),
				success: function (response) {
					console.log(response);
					eAccountingProduct.enableForm();
				},
				error: function (response) {
					console.log(response);
				}
			})
		},
		disableForm: function () {
			this.submit.attr("disabled", "disabled");
		},
		enableForm: function () {
			this.submit.removeAttr("disabled");
		},
		init: function () {
			this.bindSubmit();
		}
	};

	eAccountingProduct.init();
});
