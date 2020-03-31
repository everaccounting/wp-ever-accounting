/**
 * External imports
 */

/**
 * Internal imports
 */
import {ACTION_TYPES as types} from './action-types';
import {dispatch, select} from '../base-controls';
import {REDUCER_KEY} from './constants';

/**
 * Returns an action object used in updating the store with the provided items
 * retrieved from a request using the given querystring.
 *
 * This is a generic response action.
 *
 * @param {string} resourceName
 * @param {string} queryString  Results are stored indexed by the query
 * string generating them.
 * @param {Array<*>} response     items attached with the list.
 * @return {
 * 	{
 * 		type: string,
 * 		identifier: string,
 * 		queryString: string,
 * 		response: Array<*>
 *	}
 * } Object for action.
 */
export function receiveResponse( resourceName, queryString, response = [] ) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		response,
	};
}

/**
 * Returns an action object used in updating the store with the provided entity
 * items retrieved from a request using the given query string.
 *
 * @param {string} resourceName
 * @param {string} queryString
 * @param {Object}response
 * @return {{type: string, identifier: string, queryString: string, items:
 *   Array<BaseEntity>}} An action object.
 */
export function receiveEntityResponse( resourceName, queryString, response = {}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		response
	};
}

/**
 * Returns an action object used in updating the store with the provided entity
 * items retrieved from a request using the given query string.
 *
 * @param {string} resourceName
 * @param {Array<any>}ids
 * @param {string} queryString
 * @param {Object}response
 * @return {{type: string, identifier: string, queryString: string, items:
 *   Array<BaseEntity>}} An action object.
 */
export function receiveEntitiesById( resourceName,ids,  queryString, response = {}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		ids,
		queryString,
		response
	};
}


/**
 * Action triggering resetting all state in the store.
 */
export function* resetAllState() {
	// action for resetting the entire state
	yield {
		type: types.RESET_COLLECTION,
	};

	if ( invalidateActionsAvailable() ) {
		yield dispatch(
			'core/data',
			'invalidateResolutionForStore',
			REDUCER_KEY,
		);
		return;
	}

	// get resolvers from core/data and dispatch invalidation of each resolver.
	const resolvers = yield select(
		'core/data',
		'getCachedResolvers',
		REDUCER_KEY
	);

	// dispatch invalidation of the cached resolvers
	for ( const selector in resolvers ) {
		for ( const entry of resolvers[ selector ]._map ) {
			yield dispatch(
				'core/data',
				'invalidateResolution',
				REDUCER_KEY,
				selector,
				entry[ 0 ]
			);
		}
	}
}

/**
 * Action triggering resetting state in the store for the given selector name and
 * identifier
 *
 * @param {string} selectorName
 * @param {string} identifier
 */
export function* resetForSelectorAndIdentifier( selectorName, identifier ) {
	yield {
		type: types.RESET_COLLECTION,
		identifier,
	};

	// get resolvers from core/data
	const resolvers = yield select(
		'core/data',
		'getCachedResolvers',
		REDUCER_KEY
	);

	// dispatch invalidation of the cached resolvers for any resolver that
	// has a variation of modelName in the selector name or in the args for the
	// cached resolver.
	for ( const selector in resolvers ) {
		if (
			selectorName === selector ||
			identifierInSelector( selector, identifier )
		) {
			for ( const entry of resolvers[ selector ]._map ) {
				if ( entry[ 0 ][ 0 ] === identifier ) {
					yield dispatch(
						'core/data',
						'invalidateResolution',
						REDUCER_KEY,
						selector,
						entry[ 0 ],
					);
				}
			}
		}
	}
}

/**
 * Action triggering the state reset for the "generic" selector ('fetchAPI') and
 * it's identifier
 *
 * @param {string} identifier
 */
export function* resetGenericItemsWithIdentifier( identifier ) {
	yield* resetForSelectorAndIdentifier( 'fetchAPI', identifier );
}

/**
 * Action triggering the state reset for the entity selectors for the given
 * modelName
 *
 * @param {string} modelName
 */
export function* resetEntitiesForModelName( modelName ) {
	yield* resetForSelectorAndIdentifier( 'getEntities', modelName );
	yield* resetForSelectorAndIdentifier( 'getEntitiesByIds', modelName );
}

/**
 * Action triggering the state reset for the specific selector name, identifier
 * and query string.
 *
 * @param {string} selectorName
 * @param {string} identifier
 * @param {string} queryString
 */
export function* resetSpecificStateForSelector(
	selectorName,
	identifier,
	queryString
) {
	yield {
		type: types.RESET_COLLECTION,
		identifier,
		queryString,
	};

	yield dispatch(
		'core/data',
		'invalidateResolution',
		REDUCER_KEY,
		selectorName,
		[ identifier, queryString ]
	);
}

/**
 * Helper for determining if actions are available in the `core/data` package.
 *
 * @return {boolean}  True means additional invalidation actions available.
 */
const invalidateActionsAvailable = () => {
	return select( 'core/data' ).invalidateResolutionForStore !== undefined;
};

/**
 * Helper for determining whether the given identifier is found in the given
 * selectorName.
 *
 * @param {string} selectorName
 * @param {string} identifier
 * @return {boolean} True means it is present, false means it isn't
 */
const identifierInSelector = ( selectorName, identifier ) => {
	if ( selectorName === 'fetchAPI' ) {
		return false;
	}

	return selectorName.indexOf( identifier ) > -1;
};
