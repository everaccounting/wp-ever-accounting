import Revenues from "./table";
import EditRevenue from "./edit";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_SALES_PAGES', 'eaccounting', (pages)=> {
	pages.push({
		path: '/sales/revenues/:id/view',
		component: EditRevenue,
	});
	pages.push({
		path: '/sales/revenues/add',
		component: EditRevenue,
	});
	pages.push({
		path: '/sales/revenues',
		component: Revenues,
		name: __('Revenues'),
	});

	return pages;
});
