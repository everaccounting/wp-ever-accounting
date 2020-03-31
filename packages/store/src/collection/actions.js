import {ACTION_TYPES as types} from "./action-types";
import {dispatch, fetch, resolveSelect, select} from "../base-controls";
import {REDUCER_KEY} from "./constants";
import {REDUCER_KEY as SCHEMA_REDUCER_KEY} from "../schema/constants";

/**
 * Returns an action object used in updating the store with the provided items
 * retrieved from a request using the given querystring.
 *
 * This is a generic response action.
 *
 * @param {string} resourceName
 * @param {string} queryString  Results are stored indexed by the query
 * @param {Object} response
 * @returns {{response: {}, resourceName: *, type: string, queryString: *}}
 */
export function receiveResponse(resourceName, queryString, response = {}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		response,
	};
}


/**
 * Returns an action object used in updating the store with the provided items & total
 * retrieved from a request using the given query string.
 *
 * @param {string} resourceName
 * @param {string} queryString  Results are stored indexed by the query
 * @param {Object} response
 * @returns {{response: {total: number, items: []}, resourceName: *, type: string, queryString: *}}
 */
export function receiveCollection(resourceName, queryString, response = {items: [], total: NaN}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		queryString,
		response,
	};
}

/**
 * Returns an action object used in updating the store with the provided items & total
 * retrieved from a request using the given query string & url parts
 *
 * @param resourceName
 * @param parts
 * @param {String} queryString
 * @param response
 * @returns {{response: {total: number, items: []}, resourceName: *, type: string, queryString: *, group: *}}
 */
export function receiveCollectionWithRouteParts(resourceName, parts, queryString, response = {items: [], total: NaN}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		group: parts,
		queryString,
		response,
	};
}


/**
 * Returns an action object used in updating the store with the provided entity
 * item retrieved from a request using the given query string.
 *
 * @param resourceName
 * @param id
 * @param {String} queryString
 * @param response
 * @returns {{response: {}, resourceName: *, type: string, queryString: *, group: [*]}}
 */
export function receiveEntity(resourceName, id, queryString, response = {}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		group: [id],
		queryString,
		response,
	};
}

/**
 *
 * @param {String} resourceName
 * @param {Array} parts
 * @param {number} id
 * @param {String} queryString
 * @param {Object} response
 * @returns {{response: {}, resourceName: *, type: string, queryString: *, group: *}}
 */
export function receiveEntitiesWithRouteParts(resourceName, parts, id, queryString, response = {}) {
	return {
		type: types.RECEIVE_COLLECTION,
		resourceName,
		group: parts.concat([id]),
		queryString,
		response,
	};
}



/**
 * Action generator yielding actions for queuing an entity delete record
 * in the state.
 *
 * @param {string} resourceName
 * @param {number} entityId
 * @param {boolean} refresh
 */
export function* deleteEntityById(resourceName, entityId, refresh = true) {
	const route = yield resolveSelect(SCHEMA_REDUCER_KEY, 'getRoute', resourceName, [entityId]);
	const item = yield fetch({path: route, method: 'DELETE'});
	if (refresh)
		yield resetAllState();
	return item;
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
 * ResourceName
 *
 * @param {string} selectorName
 * @param {string} identifier
 */
export function* resetForSelectorAndResourceName( selectorName, identifier ) {
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
 * Action triggering the state reset for the specific selector name, identifier
 * and query string.
 *
 * @param {string} selectorName
 * @param {string} resourceName
 * @param {string} queryString
 */
export function* resetSpecificStateForSelector( selectorName, resourceName, queryString) {
	yield {
		type: types.RESET_COLLECTION,
		resourceName,
		queryString,
	};

	yield dispatch(
		'core/data',
		'invalidateResolution',
		REDUCER_KEY,
		selectorName,
		[ resourceName, queryString ]
	);
}

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


/**
 * Helper for determining if actions are available in the `core/data` package.
 *
 * @return {boolean}  True means additional invalidation actions available.
 */
const invalidateActionsAvailable = () => {
	return select( 'core/data' ).invalidateResolutionForStore !== undefined;
};
