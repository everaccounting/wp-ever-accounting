import Customers from './table';
import EditCustomer from './edit';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

addFilter('EA_SALES_PAGES', 'eaccounting', pages => {
	pages.push({
		path: '/sales/customers/:id/edit',
		component: EditCustomer,
	});
	pages.push({
		path: '/sales/customers/add',
		component: EditCustomer,
	});
	pages.push({
		path: '/sales/customers',
		component: Customers,
		name: __('Customers'),
	});

	return pages;
});
