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
					$.notify(response.data.message, 'success');
					eAccountingAccounts.redirect(response);
				},
				error: function(response) {
					$.notify(response.data.message, 'error');
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
		redirect:function(response){
			if(response.data && response.data.redirect){
				window.location.replace(response.data.redirect);
			}
		},
		init:function () {
			this.bindSubmit();
		}
	};

	eAccountingAccounts.init();
});
