/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import * as locksSelectors from './locks/selectors';
import * as locksActions from './locks/actions';
import customControls from './controls';
import { STORE_KEY } from './constants';
import { defaultRoutes, getMethodName } from './entities';

// The entity selectors/resolvers and actions are shortcuts to their generic equivalents
// (getEntity, getEntities, updateEntity, updateEntitiess)
// Instead of getEntity, the consumer could use more user-frieldly named selector: getPostType, getTaxonomy...
// The "kind" and the "name" of the entity are combined to generate these shortcuts.

// const entitySelectors = defaultRoutes.reduce((result, entity) => {
// 	const { name } = entity;
// 	result[getMethodName(name)] = (state, key) =>
// 		selectors.getEntity(state, name, key);
// 	result[getMethodName(name, 'get', true)] = (state, ...args) =>
// 		selectors.getEntities(state, name, ...args);
// 	result[getMethodName(name, 'getTotal', true)] = (state, ...args) =>
// 		selectors.getTotal(state, name, ...args);
// 	return result;
// }, {});
//
// const entityResolvers = defaultRoutes.reduce((result, entity) => {
// 	const { name } = entity;
// 	result[getMethodName(name)] = (key) => resolvers.getEntity(name, key);
// 	const pluralMethodName = getMethodName(name, 'get', true);
// 	result[pluralMethodName] = (...args) =>
// 		resolvers.getEntities(name, ...args);
// 	result[pluralMethodName].shouldInvalidate = (action, ...args) =>
// 		resolvers.getEntities.shouldInvalidate(action, name, ...args);
// 	result[getMethodName(name, 'getTotal', true)] = (...args) =>
// 		resolvers.getTotal(name, ...args);
// 	return result;
// }, {});
//
// const entityActions = defaultRoutes.reduce((result, entity) => {
// 	const { name } = entity;
// 	result[getMethodName(name, 'save')] = (key) =>
// 		actions.saveEntity(name, key);
// 	result[getMethodName(name, 'delete')] = (key, query) =>
// 		actions.deleteEntity(name, key, query);
// 	return result;
// }, {});

const storeConfig = {
	reducer,
	controls: { ...customControls, ...controls },
	actions: { ...actions, ...locksActions },
	selectors: { ...selectors, ...locksSelectors },
	resolvers: { ...resolvers },
};

/**
 * Store definition for the code data namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
registerStore(STORE_KEY, storeConfig);

export { STORE_KEY as STORE_NAME } from './constants';
export { default as EntityProvider } from './entity-provider';
export * from './entity-provider';
export * from './use-select-with-refresh';
export * from './use-entities';
