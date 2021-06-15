import {entities, getMethodName} from "./entities";

/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';

import { STORE_NAME } from './constants';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import controls from '../controls';
import reducer from './reducer';


const entitySelectors = entities.reduce((result, entity) => {
	const {name} = entity;
	result[getMethodName(name)] = (state, key, ...args) =>
		selectors.getEntityRecord(state, name, key, ...args);
	result[getMethodName(name, 'get', true)] = (state, ...args) =>
		selectors.getEntityRecords(state, name, ...args);
	result[getMethodName(name, 'getTotal', true)] = (state, ...args) =>
		selectors.getTotalEntityRecords(state, name, ...args);
	return result;
}, {});


const entityActions = entities.reduce((result, entity) => {
	const {name} = entity;
	result[getMethodName(name, 'save')] = (key) =>
		actions.saveEntityRecord(name, key);
	result[getMethodName(name, 'delete')] = (key, query) =>
		actions.deleteEntityRecord(name, key, query);
	return result;
}, {});


const entityResolvers = entities.reduce((result, entity) => {
	const { name } = entity;
	result[getMethodName(name)] = (key, ...args) => resolvers.getEntityRecord(name, key, ...args);
	const pluralMethodName = getMethodName(name, 'get', true);
	result[pluralMethodName] = (...args) =>
		resolvers.getEntityRecords(name, ...args);
	result[pluralMethodName].shouldInvalidate = (action, ...args) =>
		resolvers.getEntityRecords.shouldInvalidate(action, name, ...args);
	result[getMethodName(name, 'getTotal', true)] = (...args) =>
		resolvers.getTotalEntityRecords(name, ...args);
	return result;
}, {});

registerStore( STORE_NAME, {
	reducer,
	controls: controls,
	actions: { ...actions, ...entityActions },
	selectors: { ...selectors, ...entitySelectors },
	resolvers: { ...resolvers, ...entityResolvers },
} );

export const ENTITIES_STORE_NAME = STORE_NAME;
