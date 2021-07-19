/**
 * Internal dependencies
 */
import {
	apiFetch,
	apiFetchWithHeaders,
	resolveSelect,
	select,
} from '../base-controls';
import { STORE_NAME } from './constants';
import { API_NAMESPACE } from '../site-data';
/**
 * External dependencies
 */
import {
	compact,
	find,
	get,
	hasIn,
	identity,
	includes,
	pickBy,
	uniq,
} from 'lodash';
import {
	receiveSchema,
	receiveEntityError,
	receiveEntityRecords,
	receiveTotalEntityRecords,
	receiveSettings,
	receiveCurrentUser,
	receiveUserPermission,
} from './actions';
import { getNormalizedCommaSeparable } from '../utils';
/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Resolver for getSchema
 */
export function* getSchema() {
	yield resolveSelect( STORE_NAME, 'getSchemas' );
}

/**
 * Resolver for getSchemas
 */
export function* getSchemas() {
	const res = yield apiFetch( { path: API_NAMESPACE } );
	const routes = yield res && res.routes ? res.routes : {};
	const schemas = yield Object.keys( routes ).reduce( ( acc = {}, route ) => {
		const endpoint = route.replace(
			/\/\(\?P\<[a-z_]*\>\[\\*[a-z]\-?\]\+\)/g,
			''
		);
		const name = endpoint.replace( `${ API_NAMESPACE }/`, '' );
		if ( name === API_NAMESPACE || find( acc, { name } ) ) {
			return acc;
		}

		const config = get( routes[ route ], [ 'endpoints' ], [] ).reduce(
			( memo = {}, schema ) => {
				const method = schema.methods.pop();
				if ( method === 'POST' ) {
					return { ...memo, properties: schema.args };
				} else if ( method === 'GET' ) {
					return { ...memo, queryArgs: schema.args };
				}
				return memo;
			},
			{}
		);
		acc.push( {
			name,
			route: endpoint,
			...config,
		} );

		return acc;
	}, [] );
	yield receiveSchema( schemas );
}

/**
 * Requests an entity's record from the REST API.
 *
 * @param {string}           name  Entity name.
 * @param {number|string}    key   Record's key
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 */
export function* getEntityRecord( name, key = '', query = {} ) {
	const schema = yield resolveSelect( STORE_NAME, 'getSchema', name );
	if ( ! schema ) {
		throw `Could not find any schema named "${ name }" please check schema config`;
	}
	const { route, primaryKey } = schema;
	if ( query && query.hasOwnProperty( '_fields' ) ) {
		// If requesting specific fields, items and query association to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq( [
				...( getNormalizedCommaSeparable( query._fields ) || [] ),
				primaryKey,
			] ).join(),
		};
	}

	if ( query && query.hasOwnProperty( '_fields' ) ) {
		query = { ...query, include: [ key ] };

		// The resolution cache won't consider query as reusable based on the
		// fields, so it's tested here, prior to initiating the REST request,
		// and without causing `getEntities` resolution to occur.
		const hasRecords = yield select(
			STORE_NAME,
			'getEntityRecords',
			name,
			query
		);
		if ( hasRecords ) {
			return;
		}
	}
	const path = addQueryArgs( route + '/' + key, {
		...pickBy( query, identity ),
	} );
	try {
		const item = yield apiFetch( { path } );
		yield receiveEntityRecords( name, item, query, primaryKey );
		yield receiveEntityError(
			name,
			{},
			{
				...query,
				key,
			}
		);
	} catch ( error ) {
		yield receiveEntityError( name, error, {
			...query,
			key,
		} );
	}
}

/**
 * Requests the entity's records from the REST API.
 *
 * @param {string}  name   Entity name.
 * @param {Object?} query  Query Object.
 */
export function* getEntityRecords( name, query = {} ) {
	const schema = yield resolveSelect( STORE_NAME, 'getSchema', name );
	if ( ! schema ) {
		throw `Could not find any schema named "${ name }" please check schema config`;
	}
	const { route, primaryKey } = schema;
	if ( query && query.hasOwnProperty( '_fields' ) ) {
		// If requesting specific fields, items and query association to said
		// records are stored by ID reference. Thus, fields must always include
		// the ID.
		query = {
			...query,
			_fields: uniq( [
				...( getNormalizedCommaSeparable( query._fields ) || [] ),
				primaryKey,
			] ).join(),
		};
	}

	const path = addQueryArgs( route, {
		...pickBy( query, identity ),
		context: 'edit',
	} );

	try {
		const { data, headers } = yield apiFetchWithHeaders( { path } );
		const total = parseInt( headers.get( 'x-wp-total' ), 10 );
		yield receiveEntityRecords( name, data, query, primaryKey );
		yield receiveTotalEntityRecords( name, total, query );
		yield receiveEntityError( name, {}, query );
		// When requesting all fields, the list of results can be used to
		// resolve the `getItem` selector in addition to `getItems`.
		// See https://github.com/WordPress/gutenberg/pull/26575
		if ( ! query?._fields ) {
			for ( const item of data ) {
				if ( item[ primaryKey ] ) {
					yield {
						type: 'START_RESOLUTION',
						selectorName: 'getEntityRecord',
						args: [ name, item[ primaryKey ] ],
					};
					yield {
						type: 'FINISH_RESOLUTION',
						selectorName: 'getEntityRecord',
						args: [ name, item[ primaryKey ] ],
					};
				}
			}
		}
	} catch ( error ) {
		yield receiveEntityError( name, error, query );
	}
}

getEntityRecords.shouldInvalidate = ( action, name ) => {
	return (
		( action.type === 'RECEIVE_ITEMS' || action.type === 'REMOVE_ITEMS' ) &&
		action.invalidateCache &&
		name === action.name
	);
};

/**
 * Get entity count.
 *
 * @param {string} name
 * @param {Object} query
 */
export function* getTotalEntityRecords( name, query = {} ) {
	yield resolveSelect( STORE_NAME, 'getEntityRecords', name, query );
}

/**
 * Returns the Entity record error.
 *
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 * @param {number | string} recordId Key.
 */
export function* getEntityFetchError( name, query = {}, recordId = null ) {
	if ( recordId ) {
		yield resolveSelect(
			STORE_NAME,
			'getTotalEntityRecord',
			name,
			recordId,
			query
		);
	} else {
		yield resolveSelect( STORE_NAME, 'getTotalEntityRecords', name, query );
	}
}

/**
 * Retrieves options value from the options store.
 */
export function* getOptions() {
	try {
		const { route } = yield select( STORE_NAME, 'getSchema', 'settings' );
		const settings = yield apiFetch( { path: route } );
		yield receiveSettings( settings );
	} catch ( error ) {
		receiveSettings( {}, error );
	}
}

/**
 * Retrieves an option value from the options store.
 *
 **/
export function* getOption() {
	yield resolveSelect( STORE_NAME, 'getOptions' );
}

/**
 * Requests the current user from the REST API.
 */
export function* getCurrentUser() {
	const currentUser = yield apiFetch( { path: '/wp/v2/users/me' } );
	yield receiveCurrentUser( currentUser );
}

/**
 * Checks whether the current user can perform the given action on the given
 * REST resource.
 *
 * @param {string}  action   Action to check. One of: 'create', 'read', 'update',
 *                           'delete'.
 * @param {string}  resource REST resource to check, e.g. 'media' or 'posts'.
 * @param {?string} id       ID of the rest resource to check.
 */
export function* canUser( action, resource, id ) {
	const methods = {
		create: 'POST',
		read: 'GET',
		update: 'PUT',
		delete: 'DELETE',
	};

	const method = methods[ action ];
	if ( ! method ) {
		throw new Error( `'${ action }' is not a valid action.` );
	}

	const path = id ? `/wp/v2/${ resource }/${ id }` : `/wp/v2/${ resource }`;

	let response;
	try {
		response = yield apiFetch( {
			path,
			// Ideally this would always be an OPTIONS request, but unfortunately there's
			// a bug in the REST API which causes the Allow header to not be sent on
			// OPTIONS requests to /posts/:id routes.
			// https://core.trac.wordpress.org/ticket/45753
			method: id ? 'GET' : 'OPTIONS',
			parse: false,
		} );
	} catch ( error ) {
		// Do nothing if our OPTIONS request comes back with an API error (4xx or
		// 5xx). The previously determined isAllowed value will remain in the store.
		return;
	}

	let allowHeader;
	if ( hasIn( response, [ 'headers', 'get' ] ) ) {
		// If the request is fetched using the fetch api, the header can be
		// retrieved using the 'get' method.
		allowHeader = response.headers.get( 'allow' );
	} else {
		// If the request was preloaded server-side and is returned by the
		// preloading middleware, the header will be a simple property.
		allowHeader = get( response, [ 'headers', 'Allow' ], '' );
	}

	const key = compact( [ action, resource, id ] ).join( '/' );
	const isAllowed = includes( allowHeader, method );
	yield receiveUserPermission( key, isAllowed );
}
