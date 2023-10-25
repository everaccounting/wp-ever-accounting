/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * External dependencies
 */
import fastDeepEqual from 'fast-deep-equal/es6';

/**
 * Internal dependencies
 */
import { defaultEntities } from './entities';
import { ifMatchingAction, replaceAction, conservativeMapItem, getQueryParts, onSubKey } from './utils';
import { DEFAULT_PRIMARY_KEY } from './constants';

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
			return [ ...state, ...action.entities.filter( ( entity ) => entity.name ) ];
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
 * @param {Object} entity Entity config.
 *
 * @return {Function} Reducer.
 */
export function records( entity ) {
	return compose( [
		// Limit to matching action type, so we don't attempt to replace action on
		// an unhandled action.
		ifMatchingAction( ( action ) => action.name && action.name === entity.name ),
		// Inject the entity config into the action.
		replaceAction( ( action ) => {
			return {
				...action,
				key: entity.key || DEFAULT_PRIMARY_KEY,
			};
		} ),
	] )(
		combineReducers( {
			items: ( state = {}, action ) => {
				switch ( action.type ) {
					case 'RECEIVE_RECORDS':
						const { context = 'default', key = DEFAULT_PRIMARY_KEY } = action;
						return {
							...state,
							[ context ]: {
								...state[ context ],
								...action.items.reduce( ( accumulator, value ) => {
									const itemId = value[ key ];
									accumulator[ itemId ] = conservativeMapItem( state?.[ context ]?.[ itemId ], value );
									return accumulator;
								}, {} ),
							},
						};
					case 'REMOVE_RECORDS':
						return Object.fromEntries(
							Object.entries( state ).map( ( [ itemId, contextState ] ) => [ itemId, removeEntitiesById( contextState, action.itemIds ) ] )
						);
				}
				return state;
			},
			counts: ( state = {}, action ) => {
				const { stableKey } = getQueryParts( action.query );
				switch ( action.type ) {
					case 'RECEIVE_RECORDS_COUNT':
						return {
							...state,
							[ stableKey ]: parseInt( action.count, 10 ),
						};
				}
				return state;
			},
			queries: ( state = {}, action ) => {
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
			requesting: ( state = {}, action ) => {
				// using getState() here to get the current state of items state.
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
				( memo, entity ) => ( {
					...memo,
					[ entity.name ]: records( entity ),
				} ),
				{}
			)
		);
	}

	const newRecords = entitiesDataReducer( state.records, action );
	if ( newRecords === state.records && newEntities === state.entities && entitiesDataReducer === state.reducer ) {
		return state;
	}

	return {
		records: newRecords,
		entities: newEntities,
		reducer: entitiesDataReducer,
	};
};

export default reducer;
