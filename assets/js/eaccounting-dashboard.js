(function($, window, wp, document, undefined) {
	var eAccountingDashboard = {
		renderExpenseByCategories: function() {
			var period = $(this).val(),
				nonce = $(this).data('nonce');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'eaccounting_get_expense_by_category_chart',
					period: period,
					nonce: nonce,
				},
				success: function(res) {
					if (res.success) {
						eAccountingDashboard.renderDoughnutChart('ea-expense-by-categories', res.data);
					}
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				},
			});
		},
		renderIncomeByCategories: function() {
			var period = $(this).val(),
				nonce = $(this).data('nonce');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'eaccounting_get_income_by_category_chart',
					period: period,
					nonce: nonce,
				},
				success: function(res) {
					if (res.success) {
						eAccountingDashboard.renderDoughnutChart('ea-income-by-categories', res.data);
					}
				},
				error: function(errorThrown) {
					console.log(errorThrown);
				},
			});
		},
		renderDoughnutChart: function(id, response) {
			var ea_expenses = document.getElementById(id);
			new Chart(ea_expenses, {
				type: 'doughnut',
				data: {
					labels: response.labels,
					datasets: [
						{
							data: response.data,
							backgroundColor: response.background_color,
						},
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					legend: {
						display: true,
						fullWidth: true,
						position: 'right',
					},
					tooltips: {
						callbacks: {
							label: function(tooltipItem, data) {
								var allData = data.datasets[tooltipItem.datasetIndex].data;
								var tooltipLabel = data.labels[tooltipItem.index];
								var tooltipData = allData[tooltipItem.index];
								var total = 0;

								var label = tooltipLabel.split(' - ');

								for (var i in allData) {
									total += allData[i];
								}

								var tooltipPercentage = Math.round((tooltipData / total) * 100);

								return label[1] + ': ' + label[0] + ' (' + tooltipPercentage + '%)';
							},
						},
					},
				},
			});
		},

		init: function() {
			var expenseFilter = $('#expense-by-category-filter'),
				incomeFilter = $('#income-by-category-filter');
			expenseFilter.on('change', this.renderExpenseByCategories);
			expenseFilter.trigger('change');

			incomeFilter.on('change', this.renderIncomeByCategories);
			incomeFilter.trigger('change');
		},
	};
	document.addEventListener('DOMContentLoaded', function() {
		eAccountingDashboard.init();
	});
})(jQuery, window, window.wp, document, undefined);
