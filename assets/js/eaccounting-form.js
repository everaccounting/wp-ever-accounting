document.addEventListener('DOMContentLoaded', function(event) {
	var eAccountingForm = {
		select:jQuery('select.ea-select2'),
		price:jQuery('select.ea-price'),
		init:function () {
			eAccountingForm.select.select2();
			jQuery('select.ea-price').maskMoney({
				thousands : Eaccountingi18n.localization.thousands_separator,
				decimal : Eaccountingi18n.localization.decimal_mark,
				precision : Eaccountingi18n.localization.precision,
				allowZero : true,
				prefix : (Eaccountingi18n.localization.symbol_first) ? Eaccountingi18n.localization.price_symbol : '',
				suffix : (Eaccountingi18n.localization.symbol_first) ? '' : Eaccountingi18n.localization.price_symbol
			});
		}
	};

	eAccountingForm.init();
});
