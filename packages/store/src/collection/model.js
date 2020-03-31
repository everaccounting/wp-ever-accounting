/**
 * Internal dependencies
 */
import {getMethodName} from '../base-model';
import {MODELS} from '@eaccounting/data';
import {isResolving} from '../base-selectors';
import {REDUCER_KEY} from './constants';
// import {pluralModelName} from "@eaccounting/model"

/**
 * This method creates selectors for each registered model name wrapping the
 * generic source selectors.
 *
 * @param {Object<function>} source
 * @return {Object<function>} All the generated selectors for each model.
 */
export const createEntitySelectors = (source) => MODELS.reduce(
	(selectors, modelName) => {
		const methodNameForGet = getMethodName(modelName, '', 'get', true);

		selectors[methodNameForGet] = (
			state,
			queryString ='',
		) => source.getEntities(state, pluralModelName(modelName), queryString);
		selectors[getMethodName(modelName, 'Total', 'get', true)] = (
			state,
			queryString ='',
		) => source.getEntitiesTotal(state, pluralModelName(modelName), queryString);

		selectors[ getMethodName(modelName, 'byIds', 'get', true) ] = (
			state,
			ids = [],
		) => source.getEntitiesByIds(state, pluralModelName(modelName), ids);
		selectors[getMethodName(modelName, '', 'isRequesting', true)] = (
			state,
			queryString = '',
		) => isResolving(
			REDUCER_KEY,
			methodNameForGet,
			queryString
		);
		return selectors;
	},
	{},
);

/**
 * This method creates resolvers for each registered model name wrapping the
 * generic source resolvers.
 *
 * @param {Object<function>} source
 * @return {Object<function>} All the generated resolvers for each model.
 */
export const createEntityResolvers = (source) => MODELS.reduce(
	(resolvers, modelName) => {
		resolvers[getMethodName(modelName, '', 'get', true)] = (
			queryString
		) => source.getEntities(pluralModelName(modelName), queryString);
		resolvers[getMethodName(modelName, 'Total', 'get', true)] = (
			queryString
		) => source.getEntitiesTotal(pluralModelName(modelName), queryString);
		resolvers[getMethodName(modelName, 'byIds', 'get', true)] = (
			ids
		) => source.getEntitiesByIds(pluralModelName(modelName), ids);
		return resolvers;
	},
	{},
);


/**
 * Dynamic creation of actions for entities
 * @param {Object} action The action object that dynamically created functions
 * will be mapped to.
 * @return {Object} The new action object containing functions for each model.
 */
export const createActions = (action) => MODELS.reduce(
	(actions, modelName) => {
		actions[getMethodName(
			modelName,
			'',
			'create'
		)] = (entity) => action.createEntity(modelName, entity);
		actions[getMethodName(
			modelName,
			'byId',
			'delete'
		)] = (entityId) => action.deleteEntityById(modelName, entityId);
		return actions;
	},
	{}
);
