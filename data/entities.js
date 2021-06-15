/**
 * External dependencies
 */
import { upperFirst, camelCase, find } from 'lodash';

/**
 * Internal dependencies
 */
export const DEFAULT_ENTITY_KEY = 'id';

export const defaultEntities = [
	{
		name: 'accounts', //Name
		plural: 'accounts', //Name plural
		baseURL: '/', //API url
		baseURLParams: {}, //Base query args used
		key: 'name', //Primary key
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
