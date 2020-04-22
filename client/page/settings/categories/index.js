import Categories from './table';
import EditCategory from './edit';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';

addFilter('EA_SETTINGS_PAGES', 'eaccounting', pages => {
	pages.push({
		path: '/settings/categories/:id/:action',
		component: EditCategory,
	});
	pages.push({
		path: '/settings/categories/add',
		component: EditCategory,
	});
	pages.push({
		path: '/settings/categories',
		component: Categories,
		name: __('Categories'),
	});

	return pages;
});
