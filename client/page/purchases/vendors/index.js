import Vendors from "./table";
import ViewVendor from "./view";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_PURCHASES_PAGES', 'eaccounting', (pages)=> {
	pages.push({
		path: '/purchases/vendors/:id/view',
		component: ViewVendor,
	});
	pages.push({
		path: '/purchases/vendors/add',
		component: ViewVendor,
	});
	pages.push({
		path: '/purchases/vendors',
		component: Vendors,
		name: __('Vendors'),
	});

	return pages;
});
