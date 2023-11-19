/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { defaultEntities } from './entities';
import { ifMatchingAction, replaceAction, getQueryParts, conservativeMapItem, removeRecordsById } from './utils';
import { DEFAULT_KEY } from './constants';
/**
 * External dependencies
 */
import { filter, forEach } from 'lodash';
import fastDeepEqual from 'fast-deep-equal/es6';

/**
 * Reducer keeping track of the registered entities.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function entities( state = defaultEntities, action ) {
	switch ( action.type ) {
		case 'ADD_ENTITIES':
			return [ ...state, ...action.entities ];
	}

	return state;
}

/**
 * Higher Order Reducer for a given entity config. It supports:
 *
 *  - Items
 *  - Queries
 *  - Counts
 *  - Edits
 *  - Fetching
 *  - Saving
 *  - Deleting
 *
 * @param {Object} entityConfig Entity config.
 *
 * @return {Function} Reducer.
 */
function entity( entityConfig ) {
	return compose( [
		// Limit to matching action type, so we don't attempt to replace action on
		// an unhandled action.
		ifMatchingAction( ( action ) => action.name && action.name === entityConfig.name ),
		// Inject the entity config into the action.
		replaceAction( ( action ) => {
			return {
				...action,
				key: entityConfig.key || DEFAULT_KEY,
			};
		} ),
	] )(
		combineReducers( {
			items: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_RECORDS':
						const { key = DEFAULT_KEY } = action;
						const { context } = getQueryParts( action.query );
						return {
							...state,
							[ context ]: {
								...state[ context ],
								...action.records.reduce( ( accumulator, value ) => {
									const recordId = value[ key ];
									accumulator[ recordId ] = conservativeMapItem(
										state?.[ context ]?.[ recordId ],
										value
									);
									return accumulator;
								}, {} ),
							},
						};

					case 'REMOVE_RECORDS':
						return Object.fromEntries(
							Object.entries( state ).map( ( [ recordId, contextState ] ) => [
								recordId,
								removeRecordsById( contextState, action.recordIds ),
							] )
						);
				}
				return state;
			},
			queries: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_RECORDS':
						const { stableKey, context } = getQueryParts( action.query );
						const ids = action.records.reduce( ( accumulator, value ) => {
							const recordId = value[ entityConfig.key ];
							accumulator.push( recordId );
							return accumulator;
						}, [] );
						return {
							...state,
							[ context ]: {
								...state[ context ],
								[ stableKey ]: {
									data: ids,
									error: null,
								},
							},
						};

					case 'REMOVE_RECORDS':
						const newState = { ...state };
						const removedRecords = action.recordIds.reduce( ( result, itemId ) => {
							result[ itemId ] = true;
							return result;
						}, {} );
						forEach( newState, ( queryRecords, key ) => {
							newState[ key ] = filter( queryRecords, ( queryId ) => {
								return ! removedRecords[ queryId ];
							} );
						} );
						return newState;

					case 'RECEIVE_QUERY_ERROR':
						const { stableKey: errorStableKey, context: errorContext } = getQueryParts( action.query );
						return {
							...state,
							[ errorContext ]: {
								...state[ errorContext ],
								[ errorStableKey ]: {
									error: action.error,
								},
							},
						};
				}
				return state;
			},
			counts: ( state = {}, action ) => {
				const { stableKey, context } = getQueryParts( action.query );
				switch ( action.type ) {
					case 'RECEIVE_RECORDS_COUNT':
						return {
							...state,
							[ context ]: {
								...state[ context ],
								[ stableKey ]: {
									data: action.count,
									error: null,
								},
							},
						};

					case 'RECEIVE_RECORDS_ERROR':
						return {
							...state,
							[ context ]: {
								...state[ context ],
								[ stableKey ]: {
									error: action.error,
								},
							},
						};
				}
				return state;
			},
			edits: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_RECORDS':
						const nextState = { ...state };
						for ( const record of action.records ) {
							const recordId = record[ action.key ];
							const edits = nextState[ recordId ];
							if ( ! edits ) {
								continue;
							}
							const nextEdits = Object.keys( edits ).reduce( ( acc, key ) => {
								// If the edited value is still different to the persisted value,
								// keep the edited value in edits.
								if (
									// Edits are the "raw" attribute values, but records may have
									// objects with more properties, so we use `get` here for the
									// comparison.
									! fastDeepEqual( edits[ key ], record[ key ]?.raw ?? record[ key ] ) &&
									// Sometimes the server alters the sent value which means
									// we need to also remove the edits before the api request.
									( ! action.persistedEdits ||
										! fastDeepEqual( edits[ key ], action.persistedEdits[ key ] ) )
								) {
									acc[ key ] = edits[ key ];
								}
								return acc;
							}, {} );

							if ( Object.keys( nextEdits ).length ) {
								nextState[ recordId ] = nextEdits;
							} else {
								delete nextState[ recordId ];
							}
						}

						return nextState;
				}
				return state;
			},
			saving: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'SAVE_RECORD_START':
					case 'SAVE_RECORD_FINISH':
						return {
							...state,
							[ action.recordId ]: {
								pending: action.type === 'SAVE_RECORD_START',
								error: action.error,
							},
						};
				}
				return state;
			},
			deleting: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'DELETE_RECORD_START':
					case 'DELETE_RECORD_FINISH':
						return {
							...state,
							[ action.recordId ]: {
								pending: action.type === 'DELETE_RECORD_START',
								error: action.error || null,
							},
						};
				}
				return state;
			},
			fetching: ( state = {}, action ) => {
				const { type, query, key } = action;
				if ( type !== 'RECEIVE_RECORDS' ) {
					return state;
				}
				// An item is considered complete if it is received without an associated
				// fields query. Ideally, this would be implemented in such a way where the
				// complete aggregate of all fields would satisfy completeness. Since the
				// fields are not consistent across all entity types, this would require
				// introspection on the REST schema for each entity to know which fields
				// compose a complete item for that entity.
				const isCompleteQuery = ! query || ! Array.isArray( getQueryParts( query ).fields );
				return {
					...state,
					...action.records.reduce( ( result, item ) => {
						const itemId = item[ key ];

						// Defer to completeness if already assigned. Technically the
						// data may be outdated if receiving items for a field subset.
						result[ itemId ] = ! ( state[ itemId ] || isCompleteQuery );

						return result;
					}, {} ),
				};
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
export const reducer = ( state = {}, action ) => {
	const newEntities = entities( state.entities, action );
	// Generates a dynamic reducer for the entities.
	let entitiesDataReducer = state.reducer;
	if ( ! entitiesDataReducer || newEntities !== state.entities ) {
		entitiesDataReducer = combineReducers(
			Object.values( newEntities ).reduce(
				( memo, entityConfig ) => ( {
					...memo,
					[ entityConfig.name ]: entity( entityConfig ),
				} ),
				{}
			)
		);
	}

	const newData = entitiesDataReducer( state.records, action );
	if ( newData === state.records && newEntities === state.entities && entitiesDataReducer === state.reducer ) {
		return state;
	}

	return {
		records: newData,
		entities: newEntities,
		reducer: entitiesDataReducer,
	};
};

export default reducer;
