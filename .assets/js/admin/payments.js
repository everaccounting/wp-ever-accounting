(function (document, wp, $) {
	'use strict';
	$('#eac-payment-form').eac_form({
		events: {
			'change :input[name="account_id"]': function (e){
				console.log(e.target.value);
				console.log(this);
			},
		},
	});

}(document, wp, jQuery));
