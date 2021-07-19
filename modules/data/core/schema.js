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
].map( ( item ) => ( { ...defaultSchemaProperties, ...item } ) );
