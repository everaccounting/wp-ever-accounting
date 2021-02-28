/**
 * External dependencies
 */
import { groupBy, flowRight, isEqual, get } from 'lodash';

/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { ifMatchingAction, replaceAction } from './utils';
import { reducer as queriedDataReducer } from './queried-data';
import { defaultEntities, DEFAULT_ENTITY_KEY } from './entities';
import { reducer as locksReducer } from './locks';

/**
 * Higher Order Reducer for a given entity config. It supports:
 *
 *  - Fetching
 *  - Editing
 *  - Saving
 *
 * @param {Object} entityConfig  Entity config.
 *
 * @return {Function} Reducer.
 */
function entity( entityConfig ) {
	return flowRight( [
		// Limit to matching action type so we don't attempt to replace action on
		// an unhandled action.
		ifMatchingAction(
			( action ) =>
				action.name &&
				action.name === entityConfig.name
		),

		// Inject the entity config into the action.
		replaceAction( ( action ) => {
			return {
				...action,
				key: entityConfig.key || DEFAULT_ENTITY_KEY,
			};
		} ),
	] )(
		combineReducers( {
			queriedData: queriedDataReducer,

			edits: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_ITEMS':
						const nextState = { ...state };

						for ( const record of action.items ) {
							const recordId = record[ action.key ];
							const edits = nextState[ recordId ];
							if ( ! edits ) {
								continue;
							}

							const nextEdits = Object.keys( edits ).reduce(
								( acc, key ) => {
									// If the edited value is still different to the persisted value,
									// keep the edited value in edits.
									if (
										// Edits are the "raw" attribute values, but records may have
										// objects with more properties, so we use `get` here for the
										// comparison.
										! isEqual(
											edits[ key ],
											get(
												record[ key ],
												'raw',
												record[ key ]
											)
										) &&
										// Sometimes the server alters the sent value which means
										// we need to also remove the edits before the api request.
										( ! action.persistedEdits ||
											! isEqual(
												edits[ key ],
												action.persistedEdits[ key ]
											) )
									) {
										acc[ key ] = edits[ key ];
									}
									return acc;
								},
								{}
							);

							if ( Object.keys( nextEdits ).length ) {
								nextState[ recordId ] = nextEdits;
							} else {
								delete nextState[ recordId ];
							}
						}

						return nextState;

					case 'EDIT_ENTITY_RECORD':
						const nextEdits = {
							...state[ action.recordId ],
							...action.edits,
						};
						Object.keys( nextEdits ).forEach( ( key ) => {
							// Delete cleared edits so that the properties
							// are not considered dirty.
							if ( nextEdits[ key ] === undefined ) {
								delete nextEdits[ key ];
							}
						} );
						return {
							...state,
							[ action.recordId ]: nextEdits,
						};
				}

				return state;
			},

			saving: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'SAVE_ENTITY_RECORD_START':
					case 'SAVE_ENTITY_RECORD_FINISH':
						return {
							...state,
							[ action.recordId ]: {
								pending:
									action.type === 'SAVE_ENTITY_RECORD_START',
								error: action.error,
								isAutosave: action.isAutosave,
							},
						};
				}

				return state;
			},

			deleting: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'DELETE_ENTITY_RECORD_START':
					case 'DELETE_ENTITY_RECORD_FINISH':
						return {
							...state,
							[ action.recordId ]: {
								pending:
									action.type ===
									'DELETE_ENTITY_RECORD_START',
								error: action.error,
							},
						};
				}

				return state;
			},
		} )
	);
}

/**
 * Reducer keeping track of the registered entities.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function entitiesConfig( state = defaultEntities, action ) {
	switch ( action.type ) {
		case 'ADD_ENTITIES':
			return [ ...state, ...action.entities ];
	}

	return state;
}

/**
 * Reducer keeping track of the registered entities config and data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export const entities = ( state = {}, action ) => {
	const newConfig = entitiesConfig( state.config, action );
	// Generates a dynamic reducer for the entities
	let entitiesDataReducer = state.reducer;
	if ( ! entitiesDataReducer || newConfig !== state.config ) {
		const entitiesByName = groupBy( newConfig, 'name' );

		entitiesDataReducer = combineReducers(
			Object.entries( entitiesByName ).reduce(
				( memo, [ name, subEntities ] ) => {
					memo[ name ] = entity(subEntities);
					return memo;
				},
				{}
			)
		);
	}

	const newData = entitiesDataReducer( state.data, action );
	if (
		newData === state.data &&
		newConfig === state.config &&
		entitiesDataReducer === state.reducer
	) {
		return state;
	}

	return {
		reducer: entitiesDataReducer,
		data: newData,
		config: newConfig,
	};
};

export default combineReducers( {
	entities,
	locks: locksReducer,
} );
