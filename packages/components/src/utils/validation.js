/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export const is = {
	match:
		( testFn, message = '' ) =>
		( value, fieldValues ) =>
			! testFn( value, fieldValues ) && message,

	required: () => ( value ) =>
		isNilOrEmptyString( value ) &&
		__( 'This field is required', 'wp-ever-accounting' ),

	minLength: ( min ) => ( value ) =>
		!! value &&
		value.length < min &&
		`Must be at least ${ min } characters`,

	maxLength: ( max ) => ( value ) =>
		!! value && value.length > max && `Must be at most ${ max } characters`,

	notEmptyArray: () => ( value ) =>
		Array.isArray( value ) &&
		value.length === 0 &&
		'Please add at least one item',

	email: () => ( value ) =>
		!! value && ! /.+@.+\..+/.test( value ) && 'Must be a valid email',

	url: () => ( value ) =>
		!! value &&
		// eslint-disable-next-line no-useless-escape
		! /^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/.test(
			value
		) &&
		'Must be a valid URL',
	number: () => ( value ) =>
		!! value &&
		isNaN( value ) &&
		__( 'Must be a number', 'wp-ever-accounting' ),
	integer: () => ( value ) =>
		!! value &&
		! Number.isInteger( Number( value ) ) &&
		__( 'Must be an integer', 'wp-ever-accounting' ),
	positive: () => ( value ) =>
		!! value &&
		Number( value ) <= 0 &&
		__( 'Must be positive', 'wp-ever-accounting' ),
	positiveOrZero: () => ( value ) =>
		!! value &&
		Number( value ) < 0 &&
		__( 'Must be positive or zero', 'wp-ever-accounting' ),
	price: () => ( value ) =>
		!! value &&
		! /^\d+(\.\d{1,2})?$/.test( value ) &&
		__( 'Must be a valid price', 'wp-ever-accounting' ),

};

const isNilOrEmptyString = ( value ) =>
	value === undefined || value === null || value === '';

export const generateErrors = ( fieldValues, fieldValidators ) => {
	const errors = {};

	Object.entries( fieldValidators ).forEach(
		( [ fieldName, validators ] ) => {
			[ validators ].flat().forEach( ( validator ) => {
				const errorMessage = validator(
					fieldValues[ fieldName ],
					fieldValues
				);
				if ( errorMessage && ! errors[ fieldName ] ) {
					errors[ fieldName ] = errorMessage;
				}
			} );
		}
	);
	return errors;
};
