/**
 * External dependencies
 */
import createSelector from 'rememo';
import {set, map, find, get} from 'lodash';

/**
 * WordPress dependencies
 */
import {select} from '@wordpress/data';
import {addQueryArgs} from '@wordpress/url';
/**
 * Internal dependencies
 */
import {STORE_KEY} from './constants';
import {getQueriedItems, getQueriedTotal} from './queried-data';
import {getNormalizedCommaSeparable} from './utils';

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
 * Returns all available users.
 *
 * @param {Object}           state Data state.
 * @param {Object|undefined} query Optional object of query parameters to
 *                                 include with request.
 * @return {Array} Authors list.
 */
export function getUsers(state, query = {}) {
	const path = addQueryArgs('/wp/v2/users/?per_page=100', query);
	return getUserQueryResults(state, path);
}

/**
 * Returns the current user.
 *
 * @param {Object} state Data state.
 *
 * @return {Object} Current user object.
 */
export function getCurrentUser(state) {
	return state.currentUser;
}

/**
 * Returns all the users returned by a query ID.
 *
 * @param {Object} state   Data state.
 * @param {string} queryID Query ID.
 *
 * @return {Array} Users list.
 */
export const getUserQueryResults = createSelector(
	(state, queryID) => {
		const queryResults = state.users.queries[queryID];

		return map(queryResults, (id) => state.users.byId[id]);
	},
	(state, queryID) => [state.users.queries[queryID], state.users.byId]
);

/**
 * Returns the entity object given its kind and name.
 *
 * @param {Object} state   Data state.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity
 */
export function getRoute(state, name) {
	return find(state.entities.routes, {name});
}

/**
 * Returns whether the entities for the give kind are loaded.
 *
 * @param {Object} state   Data state.
 *
 * @return {boolean} Whether the entities are loaded
 */
export function getRoutes(state) {
	return state.entities.routes;
}

/**
 * Returns the Entity's record object by key. Returns `null` if the value is not
 * yet received, undefined if the value entity is known to not exist, or the
 * entity object if it exists and is received.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {number}  key   Record's key
 * @param {?Object} query Optional query.
 *
 * @return {Object?} Record.
 */
export function getEntity(state, name, key = '', query = {}) {
	const queriedState = get(state.entities.data, [name, 'queriedData']);
	if (!queriedState) {
		return undefined;
	}

	if (query === undefined) {
		// If expecting a complete item, validate that completeness.
		if (!queriedState.itemIsComplete[key]) {
			return undefined;
		}

		return queriedState.items[key];
	}

	const item = queriedState.items[key];
	if (item && query._fields) {
		const filteredItem = {};
		const fields = getNormalizedCommaSeparable(query._fields);
		for (let f = 0; f < fields.length; f++) {
			const field = fields[f].split('.');
			const value = get(item, field);
			set(filteredItem, field, value);
		}
		return filteredItem;
	}

	return item;
}

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param {Object} state  State tree.
 * @param {string} kind   Entity kind.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key.
 *
 * @return {Object?} Object with the entity's raw attributes.
 */
export const getRawEntity = createSelector(
	(state, name, key) => {
		const record = getEntity(state, name, key);
		return (
			record &&
			Object.keys(record).reduce((accumulator, _key) => {
				// Because edits are the "raw" attribute values,
				// we return those from record selectors to make rendering,
				// comparisons, and joins with edits easier.
				accumulator[_key] = get(record[_key], 'raw', record[_key]);
				return accumulator;
			}, {})
		);
	},
	(state) => [state.entities.data]
);

/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return {boolean} Whether entity records have been received.
 */
export function hasEntities(state, name, query = {}) {
	return Array.isArray(getEntities(state, name, query));
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
export function getEntities(state, name, query = {}) {
	// Queried data state is prepopulated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get(state.entities.data, [name, 'queriedData']);
	if (!queriedState) {
		return EMPTY_ARRAY;
	}
	return getQueriedItems(queriedState, query);
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @param defaults
 * @return {?Array} Records.
 */
export function getTotal(state, name, query = {}, defaults = undefined) {
	// Queried data state is prepopulated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get(state.entities.data, [name, 'queriedData']);
	if (!queriedState) {
		return defaults;
	}
	return getQueriedTotal(queriedState, query);
}

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's edits.
 */
export function getEntityEdits(state, name, recordId) {
	return get(state.entities.data, [name, 'edits', recordId]);
}

/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's non transient edits.
 */
export const getEntityNonTransientEdits = createSelector(
	(state, name, recordId) => {
		const {transientEdits} = getEntity(state, name) || {};
		const edits = getEntityEdits(state, name, recordId) || {};
		if (!transientEdits) {
			return edits;
		}
		return Object.keys(edits).reduce((acc, key) => {
			if (!transientEdits[key]) {
				acc[key] = edits[key];
			}
			return acc;
		}, {});
	},
	(state) => [state.entities.routes, state.entities.data]
);

/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record has edits or not.
 */
export function hasEditsForEntity(state, name, recordId) {
	return (
		isSavingEntity(state, name, recordId) ||
		Object.keys(getEntityNonTransientEdits(state, name, recordId))
			.length > 0
	);
}

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record, merged with its edits.
 */
export const getEditedEntity = createSelector(
	(state, name, recordId) => ({
		...getRawEntity(state, name, recordId),
		...getEntityEdits(state, name, recordId),
	}),
	(state) => [state.entities.data]
);

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export function isSavingEntity(state, name, recordId) {
	return get(
		state.entities.data,
		[name, 'saving', recordId, 'pending'],
		false
	);
}

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export function isDeletingEntity(state, name, recordId) {
	return get(
		state.entities.data,
		[name, 'deleting', recordId, 'pending'],
		false
	);
}

/**
 * Returns the specified entity record's last save error.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntitySaveError(state, name, recordId) {
	return get(state.entities.data, [name, 'saving', recordId, 'error']);
}

/**
 * Returns the specified entity record's last delete error.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's save error.
 */
export function getLastEntityDeleteError(state, name, recordId) {
	return get(state.entities.data, [name, 'deleting', recordId, 'error']);
}

/**
 * Helper indicating whether the given resourceName, selectorName, and queryString
 * is being resolved or not.
 *
 * @param {Object} state
 * @param {string} selectorName
 * @param {Object} query
 * @return {boolean} Returns true if the selector is currently requesting items.
 */
export function isRequesting(state, selectorName,name, query = {}) {
	return select(STORE_KEY).getIsResolving(selectorName, [name, query]);
}

