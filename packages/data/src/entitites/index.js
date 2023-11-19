/**
 * WordPress dependencies
 */
import { registerStore } from '@wordpress/data';
import controls from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import { STORE_NAME } from './constants';

const storeConfig = {
	reducer,
	actions,
	selectors,
	resolvers,
	controls,
};

registerStore( STORE_NAME, storeConfig );

export const ENTITIES_STORE_NAME = STORE_NAME;

export { default as useEntityRecord } from './hooks/use-entity-record';
export { default as useEntityRecords } from './hooks/use-entity-records';
