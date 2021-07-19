/**
 * Internal dependencies
 */
import { defaultSchemas, defaultSchemaProperties } from './schema';
import { ifMatchingAction, replaceAction } from '../utils';
import { reducer as queriedDataReducer } from './queried-data';
/**
 * External dependencies
 */
import { find, flowRight, get, isEmpty, isEqual } from 'lodash';

/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';

/**
 * Reducer keeping track of the registered schemas and data.
 *
 * @param {Array} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Array} Updated state.
 */
export const schemaReducer = ( state = defaultSchemas, action ) => {
	switch ( action.type ) {
		case 'RECEIVE_SCHEMA':
			return [
				...action.schema.map( ( schema ) => ( {
					...defaultSchemaProperties,
					...schema,
					...find( state, { name: schema.name } ),
				} ) ),
			];
	}

	return state;
};

/**
 * Higher Order Reducer for a given entity config. It supports:
 *
 *  - Fetching
 *  - Editing
 *  - Saving
 *
 * @param {Object} schema  Entity config.
 *
 * @return {Function} Reducer.
 */
function entity( schema ) {
	return flowRight( [
		// Limit to matching action type so we don't attempt to replace action on
		// an unhandled action.
		ifMatchingAction(
			( action ) => action.name && action.name === schema.name
		),

		// Inject the entity config into the action.
		replaceAction( ( action ) => {
			return {
				...action,
				key: schema.primaryKey,
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
 * Reducer keeping track of the registered entities config and data.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export const entities = ( state = {}, action ) => {
	const newSchemas = schemaReducer( state.schemas, action );

	// Generates a dynamic reducer for the entities
	let entitiesDataReducer = state.reducer;
	if ( ! entitiesDataReducer || newSchemas !== state.schemas ) {
		entitiesDataReducer = combineReducers(
			Object.values( newSchemas ).reduce(
				( memo, schema ) => ( {
					...memo,
					[ schema.name ]: entity( schema ),
				} ),
				{}
			)
		);
	}

	const newData = entitiesDataReducer( state.data, action );
	if (
		newData === state.data &&
		newSchemas === state.schemas &&
		entitiesDataReducer === state.reducer
	) {
		return state;
	}
	return {
		reducer: entitiesDataReducer,
		data: newData,
		schemas: newSchemas,
	};
};

/**
 * State for storing settings options.
 *
 * @param  {Object} state  Current state.
 * @param  {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

export function settings( state = {}, action ) {
	const { settings = [], type, error } = action;
	switch ( type ) {
		case 'RECEIVE_SETTINGS':
			state = {
				...state,
				...settings.reduce( ( result, setting ) => {
					return {
						...result,
						[ setting.id ]:
							isEmpty( setting.value ) && setting.default
								? setting.default
								: setting.value,
					};
				}, {} ),
				error,
			};
			break;
	}
	return state;
}

/**
 * Reducer managing current user state.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function currentUser( state = {}, action ) {
	switch ( action.type ) {
		case 'RECEIVE_CURRENT_USER':
			return action.currentUser;
		case 'RECEIVE_USER_PERMISSION':
			return {
				...state,
				permissions: { [ action.key ]: action.isAllowed },
			};
	}

	return state;
}

export default combineReducers( {
	entities,
	settings,
	currentUser,
} );
