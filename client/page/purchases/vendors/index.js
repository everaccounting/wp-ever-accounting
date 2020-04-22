import Vendors from './table';
import EditVendor from './edit';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

addFilter('EA_PURCHASES_PAGES', 'eaccounting', pages => {
	pages.push({
		path: '/purchases/vendors/:id/edit',
		component: EditVendor,
	});
	pages.push({
		path: '/purchases/vendors/add',
		component: EditVendor,
	});
	pages.push({
		path: '/purchases/vendors',
		component: Vendors,
		name: __('Vendors'),
	});

	return pages;
});
