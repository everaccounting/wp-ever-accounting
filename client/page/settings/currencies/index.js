import Currencies from './table';
import EditCurrency from './edit';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

addFilter('EA_SETTINGS_PAGES', 'eaccounting', pages => {
	pages.push({
		path: '/settings/currencies/:id/:action',
		component: EditCurrency,
	});
	pages.push({
		path: '/settings/currencies/add',
		component: EditCurrency,
	});
	pages.push({
		path: '/settings/currencies',
		component: Currencies,
		name: __('Currencies'),
	});

	return pages;
});
