import Accounts from "./table";
import EditAccount from "./edit";
import {__} from '@wordpress/i18n';
import {addFilter} from "@wordpress/hooks"


addFilter('EA_BANKING_PAGE_TABS', 'eaccounting', (pages)=> {
	pages.push({
		path: '/banking/accounts/:id/:action',
		component: EditAccount,
	});
	pages.push({
		path: '/banking/accounts/add',
		component: EditAccount,
	});
	pages.push({
		path: '/banking/accounts',
		component: Accounts,
		name: __('Accounts'),
	});

	return pages;
});
