document.addEventListener('DOMContentLoaded', function(event) {
	var eAccountingForm = {
		select:jQuery('select.ea-select2'),
		init:function () {

			if(this.select){
				this.select.select2();
			}


		}
	};

	eAccountingForm.init();
});
