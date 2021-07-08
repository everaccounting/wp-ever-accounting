/**
 * External dependencies
 */
import {
	has,
	setWith,
	clone,
	get,
	isArray,
	isInteger,
	isString,
	isPlainObject,
	isBoolean,
	isNumber,
} from 'lodash';
/**
 * Internal dependencies
 */
import { STORE_NAME } from './constants';
/**
 * This replaces regex placeholders in routes with the relevant named string
 * found in the matchIds.
 *
 * Something like:
 * /contacts/(?P<id>[\d]+)
 *
 * ..ends up as:
 * /contacts/{id}
 *
 * @param {string} route     The route to manipulate
 * @param {Array}  matchIds  An array of named ids ( [ attribute_id, id ] )
 *
 * @return {string} The route with new id placeholders
 */
export const simplifyRouteWithId = ( route, matchIds ) => {
	if ( ! Array.isArray( matchIds ) || matchIds.length === 0 ) {
		return route;
	}
	matchIds.forEach( ( matchId ) => {
		const expression = `\\(\\?P<${ matchId }>.*?\\)`;
		route = route.replace( new RegExp( expression ), `{${ matchId }}` );
	} );
	return route;
};

/**
 * Returns an array of the identifier for the named capture groups in a given
 * route.
 *
 * For example, if the route was this:
 * /ea/v1/contacts/(?P<id>[\d]+)
 *
 * ...then the following would get returned
 * ['id' ]
 *
 * @param  {string} route - The route to extract identifier names from.
 *
 * @return {Array}  An array of named route identifier names.
 */
export const getRouteIds = ( route ) => {
	const matches = route.match( /\<[a-z_]*\>/g );
	if ( ! Array.isArray( matches ) || matches.length === 0 ) {
		return [];
	}
	return matches.map( ( match ) => match.replace( /<|>/g, '' ) );
};

/**
 * Get matched route.
 *
 * @param {Object} stateSlice state
 * @param {Array} ids route parts
 * @return {string|any} Return matched route
 */
export const getRouteFromResourceEntries = ( stateSlice, ids = [] ) => {
	// convert to array for easier discovery
	stateSlice = Object.entries( stateSlice );
	const match = stateSlice.find( ( [ , schema ] ) => {
		return ids.length === schema.routeIdNames.length;
	} );
	const [ matchingRoute, routePlaceholders ] = match || [];
	// if we have a matching route, let's return it.
	if ( matchingRoute ) {
		return ids.length === 0
			? matchingRoute
			: assembleRouteWithPlaceholders(
					matchingRoute,
					routePlaceholders.routeIdNames,
					ids
			  );
	}
	return '';
};

export const getParamsFromSchema = ( stateSlice, ids = [], method = 'GET' ) => {
	// convert to array for easier discovery
	stateSlice = Object.entries( stateSlice );
	const match = stateSlice.find( ( [ , schema ] ) => {
		return ids.length === schema.routeIdNames.length;
	} );
	const [ , matchingSchema ] = match || [];
	return get( matchingSchema, [ method ], {} );
};

/**
 * For a given route, route parts and ids
 *
 * @param {string} route
 * @param {Array}  routePlaceholders
 * @param {Array}  ids
 * @return {string} Assembled route.
 */
const assembleRouteWithPlaceholders = ( route, routePlaceholders, ids ) => {
	routePlaceholders.forEach( ( part, index ) => {
		route = route.replace( `{${ part }}`, ids[ index ] );
	} );
	return route;
};

/**
 * Utility for returning whether the given path exists in the state.
 *
 * @param {Object} state The state being checked
 * @param {Array}  path  The path to check
 *
 * @return {boolean} True means this exists in the state.
 */
export const hasInState = ( state, path ) => {
	return has( state, path );
};
/**
 * Utility for updating state and only cloning objects in the path that changed.
 *
 * @param {Object} state The state being updated
 * @param {Array}  path  The path being updated
 * @param {*}      value The value to update for the path
 *
 * @return {Object} The new state
 */
export const updateState = ( state, path, value ) => {
	return setWith( clone( state ), path, value, clone );
};

/**
 * Validates the incoming value for given type.  Types allowed are:
 *
 * - integer: checks if value is an integer.
 * - number: checks if value is classified as a Number primitive or object (this
 *   means `Infinity`, `-Infinity`, and `NaN` are considered valid for this type
 * - string
 * - object - this validates as a "plainObject", that is an object created by
 *   the Object constructor or one with a [[Prototype]] of null.
 * - boolean
 * - bool: (same as boolean check)
 * - null: value must explicitly be `null`
 *
 * Note: if the passed in type does not exist, then the value is considered
 * invalid.
 *
 * @param {string|Array} type  The type or types to check
 * @param {*} value  The value being validated
 * @return {boolean}  True means the value is valid for the given type.
 */
export const validateType = ( type, value ) => {
	let valid = false;
	// account for type definitions that are an array of allowed types.
	if ( isArray( type ) ) {
		for ( const singleType of type ) {
			valid = validateType( singleType, value );
			if ( valid ) {
				break;
			}
		}
		// return right away because we've determined the validity of the type.
		return valid;
	}
	switch ( type ) {
		case 'integer':
			valid = isInteger( value );
			break;
		case 'number':
			valid = isNumber( value );
			break;
		case 'string':
			valid = isString( value );
			break;
		case 'object':
			valid = isPlainObject( value );
			break;
		case 'boolean':
		case 'bool':
			valid = isBoolean( value );
			break;
		case 'null':
			valid = value === null;
			break;
	}
	return valid;
};

/**
 * Derives the default value to use for a given type.
 *
 * @param {string} type
 * @return {*}  A value to use for the given type.
 */
export const deriveDefaultValueForType = ( type ) => {
	if ( isArray( type ) ) {
		return type.indexOf( 'null' ) > -1
			? null
			: deriveDefaultValueForType( type[ 0 ] );
	}
	switch ( type ) {
		case 'string':
			return '';
		case 'number':
		case 'integer':
			return 0;
		case 'null':
		case 'object':
			return null;
		case 'boolean':
		case 'bool':
			return false;
		case 'date-time':
			return new Date().toISOString();
	}
	return null;
};

/**
 * This gets the default value for a field from the provided schema.
 *
 * @param {string} fieldName
 * @param {Object} schema
 * @return {*} The default value for the field from the schema or if not
 * present in the schema, a derived default value from the schema type.
 */
export const getDefaultValueForField = ( fieldName, schema ) => {
	if ( schema[ fieldName ] ) {
		return schema[ fieldName ].default
			? schema[ fieldName ].default
			: deriveDefaultValueForType( schema[ fieldName ].type );
	}
	return null;
};

/**
 * This derives the "prepared" value for the given field and value.
 *
 * "Prepared" means:
 *
 * - converting to a value object if this is a field that there are defined
 *   value objects for.
 * - retrieving the "raw" value from field values that have `raw` and `rendered`
 *   or `pretty` properties.
 *
 * @param {string} fieldName
 * @param {*}  fieldValue
 * @param {Object} schema
 * not have a raw equivalent or is not a value object.
 */
export const derivePreparedValueForField = (
	fieldName,
	fieldValue,
	schema
) => {
	fieldValue = isPlainObject( fieldValue ) ? fieldValue : fieldValue;
	return maybeConvertToValueObject( fieldName, fieldValue, schema );
};

/**
 * This receives a field name, it's value and the schema and converts it to the
 * related value object IF the schema indicates it is of a type that there is a
 * known value object for.
 *
 * @param {string} fieldName
 * @param {*} fieldValue
 * @param {Object} schema
 * value is returned.
 */
export const maybeConvertToValueObject = ( fieldName, fieldValue, schema ) => {
	return fieldValue;
};

/**
 * Get Item schema based on resource name.
 *
 * @param {Object} options Request object.
 * @param {Function} options.select Select.
 * @param {string} options.resourceName Resource Name.
 * @param {Array} options.ids Request Route parts.
 * @param {string} options.method Request method.
 *
 * @return {{isError: boolean, isRequesting: boolean, params: {}, initialValue: {}, required: *[]}|{isError: boolean, isRequesting: boolean, params, initialValue: {}, required: string[]}}
 */
export const getItemSchema = ( options ) => {
	const { select, resourceName, ids = [], method = 'POST' } = options;
	const { getSchema, isResolving } = select( STORE_NAME );
	const response = {
		isRequesting: false,
		isError: false,
		schema: {},
		requiredParams: [],
		initialValue: {},
	};

	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const schema = getSchema( resourceName, ids, method );
	if ( isResolving( 'fetchSchema', [ resourceName, ids, method ] ) ) {
		return { ...response, isRequesting: true };
	}

	return {
		...response,
		schema,
		requiredParams: Object.keys( schema ).filter(
			( key ) => schema[ key ] && !! schema[ key ].required
		),
		initialValue: Object.keys( schema ).reduce( ( acc, param ) => {
			return {
				...acc,
				[ param ]: getDefaultValueForField( param, schema ),
			};
		}, {} ),
	};
};
