/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import { controls } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import customControls from './controls';
// import * as locksSelectors from './locks/selectors';
import { STORE_KEY } from './constants';
import { defaultEntities, getMethodName } from './entities';

// The entity selectors/resolvers and actions are shortcuts to their generic equivalents
// (getEntityRecord, getEntityRecords, updateEntityRecord, updateEntityRecordss)
// Instead of getEntityRecord, the consumer could use more user-frieldly named selector: getPostType, getTaxonomy...
// The "kind" and the "name" of the entity are combined to generate these shortcuts.

const entitySelectors = defaultEntities.reduce( ( result, entity ) => {
	const { name } = entity;
	result[ getMethodName( name ) ] = ( state, key ) =>
		selectors.getEntityRecord( state, name, key );
	result[ getMethodName( name, 'get', true ) ] = ( state, ...args ) =>
		selectors.getEntityRecords( state, name, ...args );
	result[ getMethodName( name, 'getTotal', true ) ] = ( state, ...args ) =>
		selectors.getEntityTotal( state, name, ...args );
	return result;
}, {} );

const entityResolvers = defaultEntities.reduce( ( result, entity ) => {
	const { name } = entity;
	result[ getMethodName( name ) ] = ( key ) =>
		resolvers.getEntityRecord( name, key );
	const pluralMethodName = getMethodName( name, 'get', true );
	result[ pluralMethodName ] = ( ...args ) =>
		resolvers.getEntityRecords( name, ...args );
	result[ pluralMethodName ].shouldInvalidate = ( action, ...args ) =>
		resolvers.getEntityRecords.shouldInvalidate( action, name, ...args );
	result[ getMethodName( name, 'getTotal', true ) ] = ( ...args ) =>
		resolvers.getEntityTotal( name, ...args );
	return result;
}, {} );

const entityActions = defaultEntities.reduce( ( result, entity ) => {
	const { name } = entity;
	result[ getMethodName( name, 'save' ) ] = ( key ) =>
		actions.saveEntityRecord( name, key );
	result[ getMethodName( name, 'delete' ) ] = ( key, query ) =>
		actions.deleteEntityRecord( name, key, query );
	return result;
}, {} );

const storeConfig = {
	reducer,
	controls: { ...customControls, ...controls },
	actions: { ...actions, ...entityActions },
	selectors: { ...selectors, ...entitySelectors },
	resolvers: { ...resolvers, ...entityResolvers },
};

/**
 * Store definition for the code data namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
registerStore( STORE_KEY, storeConfig );

export const STORE_NAME = STORE_KEY;

export { default as EntityProvider } from './entity-provider';
export * from './entity-provider';
