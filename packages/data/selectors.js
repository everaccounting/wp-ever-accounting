/**
 * External dependencies
 */
import createSelector from 'rememo';
import { set, map, find, get, filter, compact, defaultTo } from 'lodash';

/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
import deprecated from '@wordpress/deprecated';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { STORE_NAME } from './contants';
import { getQueriedItems } from './queried-data';
import { DEFAULT_ENTITY_KEY } from './entities';
import { getNormalizedCommaSeparable } from './utils';

/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 *
 * @type {Array}
 */
const EMPTY_ARRAY = [];

/**
 * Returns whether the entities for the give kind are loaded.
 *
 * @param {Object} state   Data state.
 * @param {string} name  Entity name.
 *
 * @return {boolean} Whether the entities are loaded
 */
export function getEntitiesByName( state, name ) {
	return filter( state.entities.config, { name } );
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */
export function getEntityRecords( state,  name, query ) {
	// Queried data state is prepopulated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	console.log(state.entities.data);
	// const queriedState = get( state.entities.data, [
	// 	name,
	// 	'queriedData',
	// ] );
	// if ( ! queriedState ) {
	// 	return EMPTY_ARRAY;
	// }
	// return getQueriedItems( queriedState, query );
}
