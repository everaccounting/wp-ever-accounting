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

export const defaultRoutes = [
	{
		name: 'currencies',
		key: 'code',
	},
	{
		name: 'data/currencies',
		key: 'code',
	},
	{
		name: 'settings',
		key: 'id',
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
export const getMethodName = (name, prefix = 'get', usePlural = false) => {
	const entity = find(defaultRoutes, { name });
	const methodName =
		usePlural && entity.plural
			? upperFirst(camelCase(entity.plural))
			: upperFirst(camelCase(name));
	return `${prefix}${methodName}`;
};
