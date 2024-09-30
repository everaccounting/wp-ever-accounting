jQuery(document).ready(function ($) {
	$('body').on('click', 'input#eac_business_logo', function (e) {
		e.preventDefault();
		var $this = $(this);

		const frame = wp.media({
			multiple: false,
			// images only.
			library: { type: 'image' }
		});

		frame.on('select', function () {
			const attachment = frame.state().get('selection').first().toJSON();
			$this.val(attachment.url);
		});

		frame.open();
	});

	$('.ea-financial-start').datepicker({dateFormat: 'dd-mm'});

	$('a.add-currency').on('click', function (e) {
		e.preventDefault();
		$('form#eac-add-currency').toggle();
	});

	$('#eac-currency-list').eac_form({
		events: {
			'select2:open #select-currency': 'onOpenCurrency',
			'change #select-currency': 'onAddCurrency',
		},

		onOpenCurrency: function (e) {
			var $this = $(e.target);
			console.log('Open Currency');
		},

		onAddCurrency: function (e) {
			var $this = $(e.target);
			var currency = $this.val();
			// if we found a value then enable #eac-add-currency button otherwise disable it.
			if (currency) {
				$('#eac-add-currency').removeAttr('disabled');
			} else {
				$('#eac-add-currency').attr('disabled', 'disabled');
			}
		}
	});

	$('#eac-categories-list-table .row-actions .edit a').on('click', function (e) {
		e.preventDefault();
		var $this = $(this);
		var id = $(this).data('id');
		var category = new eac.api.Category({id: id});
		category.fetch().done(function (data) {
			$this.eacmodal({
				template: 'eac-category-modal',
				model: data,
				onOpen: function () {
					this.$el.eac_form({
						events: {
							'submit': 'onSubmit',
						},
						onSubmit: function (e) {
							e.preventDefault();
							var values = this.getValues();
							category.set(values);
							category.save().done(function () {
								$this.eacmodal('close');
								window.location.reload();
							}).fail(function (data) {
									alert(data.message);
								});
						}
					})
				},
			});
		})
	});
});
