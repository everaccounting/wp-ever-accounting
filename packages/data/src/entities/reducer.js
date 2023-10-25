/**
 * WordPress dependencies
 */
import { combineReducers } from '@wordpress/data';
import { compose } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { defaultEntitiesConfig } from './entities';
import { ifMatchingAction, replaceAction, getQueryParts, conservativeMapItem, removeRecordsById } from './utils';
import { DEFAULT_KEY } from './constants';
/**
 * External dependencies
 */
import { filter, forEach } from 'lodash';

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
									accumulator[ recordId ] = conservativeMapItem( state?.[ context ]?.[ recordId ], value );
									return accumulator;
								}, {} ),
							},
						};

					case 'REMOVE_RECORDS':
						return Object.fromEntries(
							Object.entries( state ).map( ( [ recordId, contextState ] ) => [ recordId, removeRecordsById( contextState, action.recordIds ) ] )
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
						console.log( 'RECEIVE_RECORDS', { stableKey, context, ids } );
						return {
							...state,
							[ context ]: {
								...state[ context ],
								[ stableKey ]: {
									data: ids,
									error: action.error || null,
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
									error: action.error || null,
								},
							},
						};
				}
				return state;
			},
			edits: ( state = {}, action ) => {
				return state;
			},
			saving: ( state = {}, action ) => {
				return state;
			},
			deleting: ( state = {}, action ) => {
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
