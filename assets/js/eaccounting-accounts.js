document.addEventListener('DOMContentLoaded', function(event) {
	var eAccountingAccounts = {
		form:jQuery('form#ea-add-account'),
		bindSubmit:function(){
			var that = this;
			this.form.on('submit', this.handleFormSubmit);

		},
		handleFormSubmit:function(e){
			e.preventDefault();
			eAccountingAccounts.disableForm();
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
					eAccountingAccounts.enableForm();
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

	eAccountingAccounts.init();
});
