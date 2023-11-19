/**
 * External dependencies
 */
import {
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
import { DEFAULT_PRIMARY_KEY, STORE_NAME } from '../constants';

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
export const validateType = (type, value) => {
	let valid = false;
	// account for type definitions that are an array of allowed types.
	if (isArray(type)) {
		for (const singleType of type) {
			valid = validateType(singleType, value);
			if (valid) {
				break;
			}
		}
		// return right away because we've determined the validity of the type.
		return valid;
	}
	switch (type) {
		case 'integer':
			valid = isInteger(value);
			break;
		case 'number':
			valid = isNumber(value);
			break;
		case 'string':
			valid = isString(value);
			break;
		case 'object':
			valid = isPlainObject(value);
			break;
		case 'boolean':
		case 'bool':
			valid = isBoolean(value);
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
export const deriveDefaultValueForType = (type) => {
	if (isArray(type)) {
		return type.indexOf('null') > -1
			? null
			: deriveDefaultValueForType(type[0]);
	}
	switch (type) {
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
			return true;
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
export const getDefaultValueForField = (fieldName, schema) => {
	if (schema[fieldName]) {
		return schema[fieldName].default
			? schema[fieldName].default
			: deriveDefaultValueForType(schema[fieldName].type);
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
export const derivePreparedValueForField = (fieldName, fieldValue, schema) => {
	fieldValue = isPlainObject(fieldValue) ? fieldValue : fieldValue;
	return maybeConvertToValueObject(fieldName, fieldValue, schema);
};

/**
 * This receives a field name, it's value and the schema and converts it to the
 * related value object IF the schema indicates it is of a type that there is a
 * known value object for.
 *
 * @param {string} fieldName
 * @param {*} fieldValue
 * value is returned.
 */
export const maybeConvertToValueObject = (fieldName, fieldValue) => {
	return fieldValue;
};

/**
 * Get Item schema based on resource name.
 *
 * @param {{select, name: string}} options Request object.
 * @param {Function} options.select Select.
 * @param {string} options.resourceName Resource Name.
 * @param {Array} options.routeIdNames Request Route parts.
 * @param {string} options.method Request method.
 *
 * @return {{isError: boolean, isRequesting: boolean, params: {}, initialValue: {}, required: *[]}|{isError: boolean, isRequesting: boolean, params, initialValue: {}, required: string[]}}
 */
export const getSchema = (options) => {
	const { select, name } = options;
	const { getSchema, isResolving } = select(STORE_NAME);
	const response = {
		name: '',
		primaryKey: DEFAULT_PRIMARY_KEY,
		route: '',
		isRequesting: false,
		isError: false,
		properties: {},
		requiredParams: [],
		initialValue: {},
	};

	// eslint-disable-next-line @wordpress/no-unused-vars-before-return
	const entity = getSchema(name) || {};
	if (isResolving('getSchema', [name])) {
		return { ...response, isRequesting: true };
	}
	const { properties = {} } = entity;
	return {
		...response,
		...entity,
		requiredParams: Object.keys(properties).filter(
			(key) => properties[key] && !!properties[key].required
		),
		initialValue: Object.keys(properties).reduce((acc, param) => {
			return {
				...acc,
				[param]: getDefaultValueForField(param, properties),
			};
		}, {}),
	};
};
