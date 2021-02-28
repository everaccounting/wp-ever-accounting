/**
 * External dependencies
 */
import { upperFirst, camelCase, map, find, get, startCase } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {STORE_NAME} from "@eaccounting/data/contants";

/**
 * Internal dependencies
 */
import { addEntities } from './actions';


export const defaultEntities = [
	{
		name: 'media',
		plural: 'mediaItems',
		label: __( 'Media' ),
		baseURL: '/wp/v2/media',
	},
	{
		name: 'account',
		plural: 'accounts',
		label: __( 'Account' ),
		baseURL: '/ea/v1/accounts',
	},
];


/**
 * Returns the entity's getter method name given its kind and name.
 *
 * @param {string}  name      Entity name.
 * @param {string}  prefix    Function prefix.
 * @param {boolean} usePlural Whether to use the plural form or not.
 *
 * @return {string} Method name
 */
export const getMethodName = (
	name,
	prefix = 'get',
	usePlural = false
) => {
	const entity = find( defaultEntities, { name } );
	const methodName =
		usePlural && entity.plural
			? upperFirst( camelCase( entity.plural ) )
			: upperFirst( camelCase( name ) );
	return `${ prefix }${ methodName }`;
};

/**
 * Loads the kind entities into the store.
 *
 * @param {string} name  Name
 *
 * @return {Array} Entities
 */
export function* getNamedEntities( name ) {
	let entities = yield controls.select( STORE_NAME, 'getEntitiesByName', name );
	if ( entities && entities.length !== 0 ) {
		return entities;
	}

	const kindConfig = find( kinds, { name: kind } );
	if ( ! kindConfig ) {
		return [];
	}

	entities = yield kindConfig.loadEntities();
	yield addEntities( entities );

	return entities;
}
