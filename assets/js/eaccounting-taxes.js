document.addEventListener('DOMContentLoaded', function(event) {
	var eAccountingTaxes = {
		form:jQuery('form#ea-add-tax'),
		bindSubmit:function(){
			var that = this;
			this.form.on('submit', this.handleFormSubmit);

		},
		handleFormSubmit:function(e){
			e.preventDefault();
			eAccountingTaxes.disableForm();
			var formData = $(e.target).serializeArray();

			console.log($(e.target).serializeArray());
			jQuery.ajax({
				url: window.ajaxurl,
				type: 'POST',
				data: formData,
				success: function(response) {
					console.log(response);
				},
				error: function(response) {
					console.log(response);
				},
				complete: function() {
					eAccountingTaxes.enableForm();
				}
			});


		},
		disableForm:function(){
			this.form.find('input[type="submit"]').attr("disabled", "disabled");
		},
		enableForm:function(){
			this.form.find('input[type="submit"]').removeAttr("disabled");
		},
		init:function () {
			this.bindSubmit();
		}
	};

	eAccountingTaxes.init();
});
