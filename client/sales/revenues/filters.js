/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default {
	category_id: {
		title: __( 'Category' ),
		input: {
			component: 'EntitySelect',
			entityName: 'incomeCategories',
			isMulti: true,
		},
	},
	account_id: {
		title: __( 'Account' ),
		input: {
			component: 'EntitySelect',
			entityName: 'accounts',
			isMulti: true,
		},
	},
};
