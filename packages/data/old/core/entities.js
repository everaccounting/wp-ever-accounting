/**
 * Internal dependencies
 */
import { DEFAULT_PRIMARY_KEY, STORE_NAME } from './constants';
import { select } from '../base-controls';

export const defaultEntities = [
	{
		name: 'accounts',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/accounts',
	},
	{
		name: 'currencies',
		primaryKey: 'code',
		route: '/ea/v1/currencies',
	},
	{
		name: 'contacts',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/contacts',
	},
	{
		name: 'media',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/wp/v2/media',
	},
	{
		name: 'transfers',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/transfers',
	},
	{
		name: 'transactions',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/transactions',
	},
	{
		name: 'items',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/items',
	},
	{
		name: 'categories',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '/ea/v1/categories',
	},
];

/**
 * Loads the kind entities into the store.
 *
 * @param {string} name Kind
 *
 * @return {Array} Entities
 */
export function* getEntity(name) {
	const entity = yield select(STORE_NAME, 'getEntity', name);
	console.log(entity);
	if (entity && entity.length !== 0) {
		return entity;
	}

	// const kindConfig = find(kinds, { name: kind });
	// if (!kindConfig) {
	// 	return [];
	// }
	//
	// entity = yield kindConfig.loadEntities();
	// yield addEntities(entity);

	return entity;
}
