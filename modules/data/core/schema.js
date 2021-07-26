/**
 * Internal dependencies
 */
import { DEFAULT_PRIMARY_KEY } from './constants';

export const defaultSchemaProperties = {
	name: '',
	primaryKey: DEFAULT_PRIMARY_KEY,
	route: '',
	properties: {},
	queryArgs: {},
	baseURLParams: { context: 'edit' },
};

export const defaultSchemas = [
	{
		name: 'currencies',
		primaryKey: 'code',
		route: '/ea/v1/currencies',
	},
	{
		name: 'media',
		primaryKey: 'id',
		route: '/wp/v2/media',
	},
].map( ( item ) => ( { ...defaultSchemaProperties, ...item } ) );
