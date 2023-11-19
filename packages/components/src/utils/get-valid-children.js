/**
 * WordPress dependencies
 */
import { Children, isValidElement } from '@wordpress/element';

/**
 * Gets a collection of available children elements from a React component's children prop.
 *
 * @param {Node} children The children prop of a React component.
 *
 * @return {Array} An array of valid children elements.
 */
export function getValidChildren( children ) {
	if ( typeof children === 'string' ) return [ children ];

	return Children.toArray( children ).filter( ( child ) => isValidElement( child ) );
}
