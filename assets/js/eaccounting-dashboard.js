(function ($, window, wp, document, undefined) {
	var eAccountingDashboard = {
		renderExpenseByCategories:function (e) {
			var period = $(this).val(), nonce = $(this).data('nonce');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'eaccounting_get_expense_by_category_chart',
					period : period,
					nonce : nonce,
				},
				success:function(data) {
					console.log(data);
				},
				error: function(errorThrown){
					console.log(errorThrown);
				}
			});

		},
		renderIncomeByCategories:function (e) {


		},
		init:function () {
			var expenseFilter = $('#expense-by-category-filter'), incomeFilter = $('#income-by-category-filter');
			expenseFilter.on('change', this.renderExpenseByCategories);
			expenseFilter.trigger('change');

			// incomeFilter.on('change', this.renderIncomeByCategories);
			// incomeFilter.trigger('change');
		}
	};
	document.addEventListener('DOMContentLoaded', function () {
		eAccountingDashboard.init();
	});
})(jQuery, window, window.wp, document, undefined);
