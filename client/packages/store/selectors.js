import { get } from 'lodash';
import createSelector from 'rememo';
import {
	getNormalizedCommaSeparable,
	setNestedValue,
	isRawAttribute,
} from './utils';
import { getQueriedItems, getQueriedTotal } from './queried-data';

/**
 * Returns the entity config given its kind and name.
 *
 * @param {Object} state Data state.
 * @param {string} name  Entity name.
 *
 * @return {Object} Entity config.
 */
export function getEntity(state, name) {
	return get(state, ['entities', 'config'], []).find(
		(entity) => entity.name === name
	);
}

/**
 * Returns the Entity's record object by key. Returns `null` if the value is not
 * yet received, undefined if the value entity is known to not exist, or the
 * entity object if it exists and is received.
 *
 * @param state State tree
 * @param name  Entity name.
 * @param key   Record's key
 * @param query Optional query. If requesting specific
 *              fields, fields must always include the ID. For valid query parameters see the [Reference](https://developer.wordpress.org/rest-api/reference/) in the REST API Handbook and select the entity kind. Then see the arguments available "Retrieve a [Entity kind]".
 *
 * @return Record.
 */
export const getEntityRecord = createSelector(
	(state, name, key, query) => {
		const queriedState = state.entities.records?.[name]?.queriedData;
		if (!queriedState) {
			return undefined;
		}
		const context = query?.context ?? 'default';

		if (query === undefined) {
			// If expecting a complete item, validate that completeness.
			if (!queriedState.itemIsComplete[context]?.[key]) {
				return undefined;
			}

			return queriedState.items[context][key];
		}

		const item = queriedState.items[context]?.[key];
		if (item && query._fields) {
			const filteredItem = {};
			const fields = getNormalizedCommaSeparable(query._fields) ?? [];
			for (let f = 0; f < fields.length; f++) {
				const field = fields[f].split('.');
				let value = item;
				field.forEach((fieldName) => {
					value = value[fieldName];
				});
				setNestedValue(filteredItem, field, value);
			}
			return filteredItem;
		}

		return item;
	},
	(state, name, recordId, query) => {
		const context = query?.context ?? 'default';
		return [
			state.entities.records?.[name]?.queriedData?.items[context]?.[
				recordId
			],
			state.entities.records?.[name]?.queriedData?.itemIsComplete[
				context
			]?.[recordId],
		];
	}
);

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param state State tree.
 * @param name  Entity name.
 * @param key   Record's key.
 *
 * @return Object with the entity's raw attributes.
 */
export const getRawEntityRecord = createSelector(
	(state, name, key) => {
		const record = getEntityRecord(state, name, key);
		return (
			record &&
			Object.keys(record).reduce((accumulator, _key) => {
				if (isRawAttribute(getEntity(state, name), _key)) {
					// Because edits are the "raw" attribute values,
					// we return those from record selectors to make rendering,
					// comparisons, and joins with edits easier.
					accumulator[_key] = record[_key]?.raw ?? record[_key];
				} else {
					accumulator[_key] = record[_key];
				}
				return accumulator;
			}, {})
		);
	},
	(state, name, recordId, query) => {
		const context = query?.context ?? 'default';
		return [
			state.entities.config,
			state.entities.records?.[name]?.queriedData?.items[context]?.[
				recordId
			],
			state.entities.records?.[name]?.queriedData?.itemIsComplete[
				context
			]?.[recordId],
		];
	}
);

/**
 * Returns the Entity's records.
 *
 * @param {Object} state State tree
 * @param {string} name  Entity name.
 * @param {Object} query Optional terms query. If requesting specific
 *                       fields, fields must always include the ID. For valid query parameters see the [Reference](https://developer.wordpress.org/rest-api/reference/) in the REST API Handbook and select the entity kind. Then see the arguments available for "List [Entity kind]s".
 *
 * @return {Array} Records.
 */
export const getEntityRecords = (state, name, query) => {
	// Queried data state is pre-populated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist.
	const queriedState = state.entities.records?.[name]?.queriedData;
	if (!queriedState) {
		return null;
	}
	return getQueriedItems(queriedState, query) || [];
};

/**
 * Returns true if records have been received for the given set of parameters,
 * or false otherwise.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @return  {boolean} Whether records have been received.
 */
export function hasEntityRecords(state, name, query) {
	return Array.isArray(getEntityRecords(state, name, query));
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state    State tree
 * @param {string}  name     Entity name.
 * @param {?Object} query    Optional terms query.
 *
 * @param {Array}   defaults Default value.
 * @return {number} Record Count.
 */
export function getEntityRecordsTotal(
	state,
	name,
	query = {},
	defaults = undefined
) {
	const queriedState = state.entities.records?.[name]?.queriedData;
	if (!queriedState) {
		return null;
	}

	return getQueriedTotal(queriedState, query) ?? defaults;
}

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return The entity record's edits.
 */
export function getEntityRecordEdits(state, name, recordId) {
	return state.entities.records?.[name]?.edits?.[recordId];
}

/**
 * Returns the specified entity record's non transient edits.
 *
 * Transient edits don't create an undo level, and
 * are not considered for change detection.
 * They are defined in the entity's config.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return The entity record's non-transient edits.
 */
export const getEntityRecordNonTransientEdits = createSelector(
	(state, name, recordId) => {
		const { transientEdits } = getEntity(state, name) || {};
		const edits = getEntityRecordEdits(state, name, recordId) || {};
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
	(state, name, recordId) => [
		state.entities.config,
		state.entities.records?.[name]?.edits?.[recordId],
	]
);

/**
 * Returns true if the specified entity record has edits,
 * and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the specified entity record has edits.
 */
export function hasEditsForEntityRecord(state, name, recordId) {
	return (
		isSavingEntityRecord(state, name, recordId) ||
		Object.keys(getEntityRecordNonTransientEdits(state, name, recordId))
			.length > 0
	);
}

/**
 * Returns the specified entity record, merged with its edits.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return The entity record, merged with its edits.
 */
export const getEditedEntityRecord = createSelector(
	(state, name, recordId) => ({
		...getEntityRecord(state, name, recordId),
		...getEntityRecordEdits(state, name, recordId),
	}),
	(state, name, recordId, query) => {
		const context = query?.context ?? 'default';
		return [
			state.entities.config,
			state.entities.records?.[name]?.queriedData.items[context]?.[
				recordId
			],
			state.entities.records?.[name]?.queriedData.itemIsComplete[
				context
			]?.[recordId],
			state.entities.records?.[name]?.edits?.[recordId],
		];
	}
);

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export function isSavingEntityRecord(state, name, recordId) {
	return state.entities.records?.[name]?.saving?.[recordId]?.pending ?? false;
}

/**
 * Returns true if the specified entity record is deleting, and false otherwise.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is deleting or not.
 */
export function isDeletingEntityRecord(state, name, recordId) {
	return (
		state.entities.records?.[name]?.deleting?.[recordId]?.pending ?? false
	);
}

/**
 * Returns the specified entity record's last save error.
 *
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {?Object} Error object.
 */
export function getLastEntitySaveError(state, name, recordId) {
	return state.entities.records?.[name]?.saving?.[recordId]?.error;
}

/**
 * Returns the specified entity record's last delete error.
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {?Object} Error object.
 */
export function getLastEntityDeleteError(state, name, recordId) {
	return state.entities.records?.[name]?.deleting?.[recordId]?.error;
}

/**
 * Returns the current user.
 *
 * @param state Data state.
 *
 * @return Current user object.
 */
export function getCurrentUser(state) {
	return state.currentUser;
}

/**
 * Returns whether the current user can perform the given action on the given
 * REST resource.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @param {Object} state    Data state.
 * @param {string} action   Action to check. One of: 'create', 'read', 'update', 'delete'.
 * @param {string} resource REST resource to check, e.g. 'media' or 'posts'.
 * @param {number} id       Optional ID of the rest resource to check.
 *
 * @return {boolean|undefined} Whether the current user can perform the action.
 */
export function canUser(state, action, resource, id) {
	const key = [action, resource, id].filter(Boolean).join('/');
	return state.userPermissions[key];
}

/**
 * Returns whether the current user can edit the given entity.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @param {Object} state    State tree
 * @param {string} name     Entity name.
 * @param {string} recordId Record's id.
 *
 * @return {boolean|undefined} Whether the current user can edit the entity,
 */
export function canUserEditEntityRecord(state, name, recordId) {
	const entityConfig = getEntity(state, name);
	if (!entityConfig) {
		return false;
	}
	const resource = entityConfig.__unstable_rest_base;

	return canUser(state, 'update', resource, recordId);
}
