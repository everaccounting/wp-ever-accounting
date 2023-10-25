/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { defaultEntitiesConfig } from './entities';
import { ifMatchingAction, replaceAction, conservativeMapItem, removeRecordsById, getMergedItemIds, onSubKey, getQueryParts } from './utils';
import { DEFAULT_KEY } from './constants';
/**
 * External dependencies
 */
import fastDeepEqual from 'fast-deep-equal/es6';

/**
 * Reducer keeping track of the registered entities.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
export function entitiesConfig( state = defaultEntitiesConfig, action ) {
	switch ( action.type ) {
		case 'ADD_ENTITIES':
			return [ ...state, ...action.entities ];
	}

	return state;
}

/**
 * Reducer tracking queries state, keyed by stable query key. Each reducer
 * query object includes `itemIds` and `requestingPageByPerPage`.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Next state.
 */
const receiveQueries = compose( [
	// Limit to matching action type so we don't attempt to replace action on
	// an unhandled action.
	ifMatchingAction( ( action ) => 'query' in action ),

	// Inject query parts into action for use both in `onSubKey` and reducer.
	replaceAction( ( action ) => {
		// `ifMatchingAction` still passes on initialization, where state is
		// undefined and a query is not assigned. Avoid attempting to parse
		// parts. `onSubKey` will omit by lack of `stableKey`.
		if ( action.query ) {
			return {
				...action,
				...getQueryParts( action.query ),
			};
		}

		return action;
	} ),

	// onSubKey( 'context' ),

	// Queries shape is shared, but keyed by query `stableKey` part. Original
	// reducer tracks only a single query object.
	// onSubKey( 'stableKey' ),
] )( ( state = null, action ) => {
	const { type, page, perPage, key = DEFAULT_KEY } = action;

	if ( type !== 'RECEIVE_RECORDS' ) {
		return state;
	}

	return getMergedItemIds(
		state || [],
		action.records.map( ( item ) => item[ key ] ),
		page,
		perPage
	);
} );

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
						const { context = 'default', key } = action;
						return {
							...state,
							[ context ]: {
								...state[ context ],
								...action.records.reduce( ( accumulator, value ) => {
									const recordId = value[ key ];
									accumulator[ recordId ] = conservativeMapItem( state?.[ context ]?.[ recordId ], value );
									return accumulator;
								}, {} ),
							},
						};
					case 'REMOVE_RECORDS':
						return Object.fromEntries(
							Object.entries( state ).map( ( [ itemId, contextState ] ) => [ itemId, removeRecordsById( contextState, action.itemIds ) ] )
						);
				}
				return state;
			},
			queries: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_RECORDS':
						const { context = 'default', query } = action;
						const { stableKey } = getQueryParts( query );
						return {
							...state,
							[ context ]: {
								...state[ context ],
								[ stableKey ]: {
									...state[ context ]?.[ stableKey ],
									//push only the ids of the records.
									...action.records.map( ( record ) => record[ action.key ] ),
								},
							},
						};
					case 'REMOVE_RECORDS':
						const removedItems = action.itemIds.reduce( ( result, itemId ) => {
							result[ itemId ] = true;
							return result;
						}, {} );

						return Object.fromEntries(
							Object.entries( state ).map( ( [ queryGroup, contextQueries ] ) => [
								queryGroup,
								Object.fromEntries(
									Object.entries( contextQueries ).map( ( [ query, queryItems ] ) => [
										query,
										queryItems.filter( ( queryId ) => ! removedItems[ queryId ] ),
									] )
								),
							] )
						);
				}

				return state;
			},
			counts: ( state = {}, action ) => {
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
									( ! action.persistedEdits || ! fastDeepEqual( edits[ key ], action.persistedEdits[ key ] ) )
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
			fetching: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'GET_RECORD_START':
					case 'GET_RECORD_FINISH':
						return {
							...state,
							[ action.recordId ]: {
								pending: action.type === 'GET_RECORD_START',
								error: action.error || null,
							},
						};
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
			errors: ( state = {}, action ) => {
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
export const reducer = ( state = {}, action ) => {
	const newConfig = entitiesConfig( state.config, action );
	// Generates a dynamic reducer for the entities.
	let entitiesDataReducer = state.reducer;
	if ( ! entitiesDataReducer || newConfig !== state.config ) {
		entitiesDataReducer = combineReducers(
			Object.values( newConfig ).reduce(
				( memo, entityConfig ) => ( {
					...memo,
					[ entityConfig.name ]: entity( entityConfig ),
				} ),
				{}
			)
		);
	}

	const newData = entitiesDataReducer( state.records, action );
	if ( newData === state.records && newConfig === state.config && entitiesDataReducer === state.reducer ) {
		return state;
	}

	return {
		records: newData,
		config: newConfig,
		reducer: entitiesDataReducer,
	};
};

export default reducer;
