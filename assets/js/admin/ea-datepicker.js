jQuery(document).ready(function($) {
	var datePickerOptions = {
		altFormat: 'yy-mm-dd',
	};

	$('input.ea-date-control').each(function() {
		var $hidden_input = $('<input />', { type: 'hidden', name: $(this).attr('name') }).insertAfter($(this));
		$(this).attr('name', $(this).attr('name') + '-datepicker');
		$(this).keyup(function() {
			if ('' === $(this).val()) {
				$hidden_input.val('');
			}
		});
		$(this).datepicker($.extend({}, datePickerOptions, { altField: $hidden_input }));
		if ($(this).val()) {
			var dateParts = $(this)
				.val()
				.split('-');
			if (3 === dateParts.length) {
				var selectedDate = new Date(
					parseInt(dateParts[0], 10),
					parseInt(dateParts[1], 10) - 1,
					parseInt(dateParts[2], 10)
				);
				$(this).datepicker('setDate', selectedDate);
			}
		}
	});
});
