/**
 * WordPress dependencies
 */
import { createRegistrySelector } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { STORE_NAME, EMPTY_ARRAY } from './constants';
import {
	getQueriedError,
	getQueriedItems,
	getQueriedTotal,
} from './queried-data';

/**
 * External dependencies
 */
import { compact, find, get, isEmpty, set } from 'lodash';
import { sprintf } from '@wordpress/i18n';
import { getNormalizedCommaSeparable } from '../utils';
import createSelector from 'rememo';

/**
 * Get all entities.
 */
export const getSchema = createRegistrySelector(
	( select ) => ( state, name ) => {
		state = state.entities.schemas;
		const hasResolved = select( STORE_NAME ).hasFinishedResolution(
			'getSchemas'
		);
		const schema = find( state, { name } );

		if ( isEmpty( schema ) && hasResolved ) {
			throw new Error(
				sprintf(
					'There is no route for the given schema name (%s) in the store',
					name
				)
			);
		}

		return schema;
	}
);

/**
 * Return all the entities in store.
 *
 * @param state
 * @return {Array} An array of all entities.
 */
export const getSchemas = createRegistrySelector( ( select ) => ( state ) => {
	state = state.entities.schemas;
	const hasResolved = select( STORE_NAME ).hasFinishedResolution(
		'getSchemas'
	);
	if ( isEmpty( state ) ) {
		if ( hasResolved ) {
			throw new Error(
				sprintf(
					'There is no entities for the given namespace (%s) in the store',
					'/ea/v1'
				)
			);
		}
		return [];
	}

	return state;
} );

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {string}  key   Primary key.
 * @param {?Object} query Optional terms query.
 *
 * @return {?Array} Records.
 */
export function getEntityRecord( state, name, key = '', query = {} ) {
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return undefined;
	}

	if ( query === undefined ) {
		// If expecting a complete item, validate that completeness.
		if ( ! queriedState.itemIsComplete[ key ] ) {
			return undefined;
		}

		return queriedState.items[ key ];
	}
	const item = queriedState.items[ key ];
	if ( item && query._fields ) {
		const filteredItem = {};
		const fields = getNormalizedCommaSeparable( query._fields );
		for ( let f = 0; f < fields.length; f++ ) {
			const field = fields[ f ].split( '.' );
			const value = get( item, field );
			set( filteredItem, field, value );
		}
		return filteredItem;
	}

	return item;
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
export function getEntityRecords( state, name, query ) {
	// Queried data state is populated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return EMPTY_ARRAY;
	}
	const items = getQueriedItems( queriedState, query );
	if ( ! items ) {
		return EMPTY_ARRAY;
	}
	return items;
}

/**
 * Returns the Entity's records.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 *
 * @param {Array} defaults Default value.
 * @return {number} Record Count.
 */
export function getTotalEntityRecords(
	state,
	name,
	query = {},
	defaults = undefined
) {
	// Queried data state is populated for all known entities. If this is not
	// assigned for the given parameters, then it is known to not exist. Thus, a
	// return value of an empty array is used instead of `null` (where `null` is
	// otherwise used to represent an unknown state).
	const queriedState = get( state.entities.data, [ name, 'queriedData' ] );
	if ( ! queriedState ) {
		return defaults;
	}
	return getQueriedTotal( queriedState, query );
}

/**
 * Returns the Entity record error.
 *
 * @param {Object}  state State tree
 * @param {string}  name  Entity name.
 * @param {?Object} query Optional terms query.
 * @param {number | string} recordId Key.
 *
 * @return {Object} Error.
 */
export function getEntityFetchError(
	state,
	name,
	query = {},
	recordId = null
) {
	const queriedState = get( state.entities.data, [
		name,
		'queriedData',
		'errors',
	] );
	return getQueriedError( queriedState, { ...query, key: recordId } );
}

/**
 * Returns true if the specified entity record is saving, and false otherwise.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {boolean} Whether the entity record is saving or not.
 */
export function isSavingEntityRecord( state, name, recordId ) {
	return get(
		state.entities.data,
		[ name, 'saving', recordId, 'pending' ],
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
export function isDeletingEntityRecord( state, name, recordId ) {
	return get(
		state.entities.data,
		[ name, 'deleting', recordId, 'pending' ],
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
export function getEntityRecordSaveError( state, name, recordId ) {
	return get( state.entities.data, [ name, 'saving', recordId, 'error' ] );
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
export function getEntityRecordDeleteError( state, name, recordId ) {
	return get( state.entities.data, [ name, 'deleting', recordId, 'error' ] );
}

/**
 * Returns the entity's record object by key,
 * with its attributes mapped to their raw values.
 *
 * @param {Object} state  State tree.
 * @param {string} name   Entity name.
 * @param {number} key    Record's key.
 *
 * @return {Array} Object with the entity's raw attributes.
 */
export const getRawEntityRecord = createSelector(
	( state, name, key ) => {
		const record = getEntityRecord( state, name, key );
		return (
			record &&
			Object.keys( record ).reduce( ( accumulator, _key ) => {
				// Because edits are the "raw" attribute values,
				// we return those from record selectors to make rendering,
				// comparisons, and joins with edits easier.
				accumulator[ _key ] = get(
					record[ _key ],
					'raw',
					record[ _key ]
				);
				return accumulator;
			}, {} )
		);
	},
	( state ) => [ state.entities.data ]
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
export function hasEntityRecords( state, name, query = {} ) {
	const records = getEntityRecords( state, name, query );
	return Array.isArray( records ) && ! isEmpty( records );
}

/**
 * Returns the  list of dirty entity records.
 *
 * @param {Object} state State tree.
 *
 * @type {*|(function(): *)}
 */
export const getDirtyEntityRecords = createSelector(
	( state ) => {
		const {
			entities: { data },
		} = state;
		const dirtyRecords = [];
		Object.keys( data ).forEach( ( name ) => {
			const primaryKeys = Object.keys(
				data[ name ].edits
			).filter( ( primaryKey ) =>
				hasEditsForEntityRecord( state, name, primaryKey )
			);

			if ( primaryKeys.length ) {
				const schema = getSchema( name );
				primaryKeys.forEach( ( primaryKey ) => {
					const entityRecord = getEditedEntityRecord(
						state,
						name,
						primaryKey
					);
					dirtyRecords.push( {
						// We avoid using primaryKey because it's transformed into a string
						// when it's used as an object key.
						key: entityRecord[ schema.primaryKey ],
						name,
					} );
				} );
			}
		} );
		return dirtyRecords;
	},
	( state ) => [ state.entities.data ]
);

/**
 * Returns the specified entity record's edits.
 *
 * @param {Object} state    State tree.
 * @param {string} name     Entity name.
 * @param {number} recordId Record ID.
 *
 * @return {Object?} The entity record's edits.
 */
export function getEntityRecordEdits( state, name, recordId ) {
	return get( state.entities.data, [ name, 'edits', recordId ] );
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
export const getEntityRecordNonTransientEdits = createSelector(
	( state, name, recordId ) => {
		const { transientEdits } = getSchema( name ) || {};
		const edits = getEntityRecordEdits( state, name, recordId ) || {};
		if ( ! transientEdits ) {
			return edits;
		}
		return Object.keys( edits ).reduce( ( acc, key ) => {
			if ( ! transientEdits[ key ] ) {
				acc[ key ] = edits[ key ];
			}
			return acc;
		}, {} );
	},
	( state ) => [ state.entities.schemas, state.entities.data ]
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
export function hasEditsForEntityRecord( state, name, recordId ) {
	return (
		isSavingEntityRecord( state, name, recordId ) ||
		Object.keys( getEntityRecordNonTransientEdits( state, name, recordId ) )
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
export const getEditedEntityRecord = createSelector(
	( state, name, recordId ) => ( {
		...getRawEntityRecord( state, name, recordId ),
		...getEntityRecordEdits( state, name, recordId ),
	} ),
	( state ) => [ state.entities.data ]
);

/**
 * Retrieves options value from the options store.
 *
 * @param {Object}   state  State param added by wp.data.
 * @return {*}  The value present in the settings state for the given name.
 */
export function getOptions( state ) {
	return state.settings;
}

/**
 * Retrieves an option value from the options store.
 *
 * @param {Object}   state  State param added by wp.data.
 * @param {string}   name   The identifier for the setting.
 * @param {*}    [fallback=false]  The value to use as a fallback if the setting is not in the state.
 * @param {Function} [filter=( val ) => val]  A callback for filtering the value before it's returned. Receives both the found value (if it exists for the key) and the provided fallback arg.
 *
 * @return {*}  The value present in the settings state for the given name.
 */
export const getOption = createRegistrySelector(
	() => ( state, name, fallback = false, filter = ( val ) => val ) => {
		const value = get( state.settings, [ name ] ) || fallback;
		return filter( value, fallback );
	}
);

/**
 * Get default currency
 */
export const getDefaultCurrency = createRegistrySelector( ( select ) => () => {
	const code = select( STORE_NAME ).getOption( 'default_currency', 'USD' );
	if ( code ) {
		return select( STORE_NAME ).getEntityRecord( 'currencies', code );
	}
	return {};
} );

/**
 * Get default currency
 */
export const getDefaultAccount = createRegistrySelector( ( select ) => () => {
	const accountId = select( STORE_NAME ).getOption( 'default_account' );
	if ( accountId ) {
		return select( STORE_NAME ).getEntityRecord( 'accounts', accountId );
	}

	return {};
} );

/**
 * Returns whether the current user can perform the given action on the given
 * REST resource.
 *
 * Calling this may trigger an OPTIONS request to the REST API via the
 * `canUser()` resolver.
 *
 * https://developer.wordpress.org/rest-api/reference/
 *
 * @param {Object}   state            Data state.
 * @param {string}   action           Action to check. One of: 'create', 'read', 'update', 'delete'.
 * @param {string}   resource         REST resource to check, e.g. 'media' or 'posts'.
 * @param {string=}  id               Optional ID of the rest resource to check.
 *
 * @return {boolean|undefined} Whether or not the user can perform the action,
 *                             or `undefined` if the OPTIONS request is still being made.
 */
export function canUser( state, action, resource, id ) {
	const key = compact( [ action, resource, id ] ).join( '/' );
	return get( state, [ 'currentUser', 'permissions', key ] );
}

/**
 * Returns the current user.
 *
 * @param {Object} state Data state.
 *
 * @return {Object} Current user object.
 */
export function getCurrentUser( state ) {
	return state.currentUser;
}
