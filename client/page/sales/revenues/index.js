import Revenues from "./table";
import ViewRevenue from "./view";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_SALES_PAGES', 'eaccounting', (pages)=> {
	pages.push({
		path: '/sales/revenues/:id/view',
		component: ViewRevenue,
	});
	// pages.push({
	// 	path: '/sales/revenues/add',
	// 	component: EditAccount,
	// });
	pages.push({
		path: '/sales/revenues',
		component: Revenues,
		name: __('Revenues'),
	});

	return pages;
});
