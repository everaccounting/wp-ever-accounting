import Transfers from "./table";
import EditTransfer from "./edit";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"

addFilter('EA_BANKING_PAGE_TABS', 'eaccounting', (pages)=> {
	pages.push({
		path: '/banking/transfers',
		component: Transfers,
		name: __('Transfers'),
	});

	return pages;
});
