import Payments from "./table";
import EditPayment from "./edit";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_PURCHASES_PAGES', 'eaccounting', (pages)=> {
	pages.push({
		path: '/purchases/payments/:id/view',
		component: EditPayment,
	});
	pages.push({
		path: '/purchases/payments/add',
		component: EditPayment,
	});
	pages.push({
		path: '/purchases/payments',
		component: Payments,
		name: __('Payments'),
	});

	return pages;
});
