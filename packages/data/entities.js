/**
 * External dependencies
 */
import { upperFirst, camelCase, find } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
export const DEFAULT_ENTITY_KEY = 'id';

export const defaultEntities = [
	{
		name: 'product',
		endpoint: 'wc/v3/products',
		plural: 'products',
		label: __( 'Products' ),
		key: 'id',
	},
	{
		name: 'category',
		endpoint: '/wp/v2/users',
		plural: 'categories',
		label: __( 'Category' ),
		key: 'id',
	},
	{
		name: 'account',
		endpoint: '/ea/v1/accounts',
		plural: 'accounts',
		label: __( 'Accounts' ),
		key: 'id',
	},
	{
		name: 'currency',
		endpoint: '/ea/v1/currencies',
		plural: 'currencies',
		label: __( 'Currency' ),
		key: 'code',
	},
];

/**
 * Returns the entity's getter method name given its kind and name.
 *
 * @param {string}  name      Entity name.
 * @param {string}  prefix    Function prefix.
 * @param {boolean} usePlural Whether to use the plural form or not.
 *
 * @return {string} Method name
 */
export const getMethodName = ( name, prefix = 'get', usePlural = false ) => {
	const entity = find( defaultEntities, { name } );
	const methodName =
		usePlural && entity.plural
			? upperFirst( camelCase( entity.plural ) )
			: upperFirst( camelCase( name ) );
	return `${ prefix }${ methodName }`;
};
