import {QUERY_DEFAULTS, API_NAMESPACE} from '../constants';
import {camelCase, find, upperFirst} from "lodash";
import { applyFilters } from '@wordpress/hooks';
/**
 * Internal dependencies
 */
export const DEFAULT_ENTITY_KEY = 'id';

export const entities = applyFilters('eaccounting_entities',[
	{
		name: 'account', //Always singular
		plural: 'accounts', //Name plural
		endpoint: `${API_NAMESPACE}/accounts`, //API url
		queryDefaults: QUERY_DEFAULTS, //Base query args used
		key: 'id', //Primary key
		preSave:(x)=>x,
		preEdit:(x)=>x,
		preDelete:(x)=>x,
		preGet:(x)=>x,
	},
	{
		name: 'item',
		plural: 'items',
		endpoint: `${API_NAMESPACE}/items`,
		queryDefaults: {},
		key: 'id',
	},
	{
		name: 'currency',
		plural: 'currencies',
		endpoint: `${API_NAMESPACE}/currencies`,
		queryDefaults: {},
		key: 'code',
	},
]);

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
	const entity = find(entities, { name });
	const methodName =
		usePlural && entity.plural
			? upperFirst(camelCase(entity.plural))
			: upperFirst(camelCase(name));
	return `${prefix}${methodName}`;
};
