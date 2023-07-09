/**
 * WordPress dependencies
 */
import { createReduxStore, register } from '@wordpress/data';

import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { STORE_NAME as CORE_STORE_NAME } from './constants';
import { defaultEntities, getMethodName } from './entities';

const entitySelectors = defaultEntities.reduce((result, entity) => {
	const { name } = entity;
	result[getMethodName(name)] = (state, key, query) =>
		selectors.getEntityRecord(state, name, key, query);
	result[getMethodName(name, 'get', true)] = (state, query) =>
		selectors.getEntityRecords(state, name, query);
	return result;
}, {});

const entityResolvers = defaultEntities.reduce((result, entity) => {
	const { name } = entity;
	result[getMethodName(name)] = (key, query) =>
		resolvers.getEntityRecord(name, key, query);
	const pluralMethodName = getMethodName(name, 'get', true);
	result[pluralMethodName] = (...args) =>
		resolvers.getEntityRecords(name, ...args);
	result[pluralMethodName].shouldInvalidate = (action) =>
		resolvers.getEntityRecords.shouldInvalidate(action, name);
	return result;
}, {});

const entityActions = defaultEntities.reduce((result, entity) => {
	const { name } = entity;
	result[getMethodName(name, 'save')] = (key) =>
		actions.saveEntityRecord(name, key);
	result[getMethodName(name, 'delete')] = (key, query) =>
		actions.deleteEntityRecord(name, key, query);
	return result;
}, {});

const storeConfig = () => ({
	reducer,
	actions: { ...actions, ...entityActions },
	selectors: { ...selectors, ...entitySelectors },
	resolvers: { ...resolvers, ...entityResolvers },
});

/**
 * Store definition for the code data namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 */
export const store = createReduxStore(CORE_STORE_NAME, storeConfig());
register(store);

export * from './hooks';
