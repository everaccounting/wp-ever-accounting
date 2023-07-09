/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
import { defaultEntities } from './entities';
import { DEFAULT_PRIMARY_KEY } from './constants';
import ifMatchingAction from './utils/if-matching-action';
import replaceAction from './utils/replace-action';
import { reducer as queriedDataReducer } from './queried-data';
import { flowRight, find } from 'lodash';
import fastDeepEqual from 'fast-deep-equal/es6';
/**
 * Reducer keeping track of the registered entities.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function entitiesConfig(state = defaultEntities, action) {
	switch (action.type) {
		case 'ADD_ENTITIES':
			// return [
			// 	...defaultEntities,
			// 	...action.entities.map((config) => ({
			// 		...defaultEntities,
			// 		...config,
			// 		...find(state, { name: config.name }),
			// 	})),
			// ];
			return [...state, ...action.entities];
	}

	return state;
}

/**
 * Higher Order Reducer for a given entity config. It supports:
 *
 *  - Fetching
 *  - Editing
 *  - Saving
 *
 * @param {Object} config Entity config.
 *
 * @return {Function} Reducer.
 */
function entity(config) {
	return flowRight([
		// Limit to matching action type, so we don't attempt to replace action on
		// an unhandled action.
		ifMatchingAction(
			(action) => action.name && action.name === config.name
		),

		// Inject the entity config into the action.
		replaceAction((action) => {
			return {
				...action,
				key: config.key || DEFAULT_PRIMARY_KEY,
			};
		}),
	])(
		combineReducers({
			queriedData: queriedDataReducer,

			edits: (state = {}, action) => {
				switch (action.type) {
					case 'RECEIVE_ITEMS':
						const context = action?.query?.context ?? 'default';
						if (context !== 'default') {
							return state;
						}
						const nextState = { ...state };

						for (const record of action.items) {
							const recordId = record[action.key];
							const edits = nextState[recordId];
							if (!edits) {
								continue;
							}

							const nextEdits = Object.keys(edits).reduce(
								(acc, key) => {
									// If the edited value is still different to the persisted value,
									// keep the edited value in edits.
									if (
										// Edits are the "raw" attribute values, but records may have
										// objects with more properties, so we use `get` here for the
										// comparison.
										!fastDeepEqual(
											edits[key],
											record[key]?.raw ?? record[key]
										) &&
										// Sometimes the server alters the sent value which means
										// we need to also remove the edits before the api request.
										(!action.persistedEdits ||
											!fastDeepEqual(
												edits[key],
												action.persistedEdits[key]
											))
									) {
										acc[key] = edits[key];
									}
									return acc;
								},
								{}
							);

							if (Object.keys(nextEdits).length) {
								nextState[recordId] = nextEdits;
							} else {
								delete nextState[recordId];
							}
						}

						return nextState;

					case 'EDIT_ENTITY_RECORD':
						const nextEdits = {
							...state[action.recordId],
							...action.edits,
						};
						Object.keys(nextEdits).forEach((key) => {
							// Delete cleared edits so that the properties
							// are not considered dirty.
							if (nextEdits[key] === undefined) {
								delete nextEdits[key];
							}
						});
						return {
							...state,
							[action.recordId]: nextEdits,
						};
				}

				return state;
			},

			saving: (state = {}, action) => {
				switch (action.type) {
					case 'SAVE_ENTITY_RECORD_START':
					case 'SAVE_ENTITY_RECORD_FINISH':
						return {
							...state,
							[action.recordId]: {
								pending:
									action.type === 'SAVE_ENTITY_RECORD_START',
								error: action.error,
							},
						};
				}

				return state;
			},

			deleting: (state = {}, action) => {
				switch (action.type) {
					case 'DELETE_ENTITY_RECORD_START':
					case 'DELETE_ENTITY_RECORD_FINISH':
						return {
							...state,
							[action.recordId]: {
								pending:
									action.type ===
									'DELETE_ENTITY_RECORD_START',
								error: action.error,
							},
						};
				}

				return state;
			},
		})
	);
}

/**
 * Reducer keeping track of the registered entities config and data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export const entities = (state = {}, action) => {
	const newConfig = entitiesConfig(state.config, action);

	// Generates a dynamic reducer for the entities
	let entitiesDataReducer = state.reducer;
	if (!entitiesDataReducer || newConfig !== state.config) {
		entitiesDataReducer = combineReducers(
			Object.values(newConfig).reduce(
				(memo, config) => ({
					...memo,
					[config.name]: entity(config),
				}),
				{}
			)
		);
	}

	const newData = entitiesDataReducer(state.records, action);
	if (
		newData === state.records &&
		newConfig === state.config &&
		entitiesDataReducer === state.reducer
	) {
		return state;
	}
	return {
		reducer: entitiesDataReducer,
		records: newData,
		config: newConfig,
	};
};

/**
 * State which tracks whether the user can perform an action on a REST
 * resource.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function userPermissions(state = {}, action) {
	switch (action.type) {
		case 'RECEIVE_USER_PERMISSION':
			return {
				...state,
				[action.key]: action.isAllowed,
			};
	}

	return state;
}

export default combineReducers({
	entities,
	userPermissions,
});
