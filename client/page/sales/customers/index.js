import Customers from "./table";
import ViewCustomer from "./view";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_SALES_PAGES', 'eaccounting', (pages)=> {
	pages.push({
		path: '/sales/customers/:id/view',
		component: ViewCustomer,
	});
	pages.push({
		path: '/sales/customers/add',
		component: ViewCustomer,
	});
	pages.push({
		path: '/sales/customers',
		component: Customers,
		name: __('Customers'),
	});

	return pages;
});
