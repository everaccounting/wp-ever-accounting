/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { select } from '@wordpress/data';

/**
 * Invokes the selector for whether a given selectorName in a given registered
 * reducer store is in the midst of resolving.
 *
 * @param {string} storeKey
 * @param {string} selectorName
 * @param {*[]} args
 * @return {boolean}  Whether resolution is in progress.
 */
export const isResolving = ( storeKey, selectorName, ...args ) => {
	return select( 'core/data' ).isResolving( storeKey, selectorName, args );
};

/**
 * Invokes the selector for whether a given selectorName in a given registered
 * reducer store has finished resolving.
 *
 * @param {string} storeKey
 * @param {string} selectorName
 * @param {*[]} args
 * @return {boolean} Whether resolution has completed.
 */
export const hasFinishedResolving = ( storeKey, selectorName, ...args ) => {
	return select( 'core/data' ).hasFinishedResolution(
		storeKey,
		selectorName,
		args
	);
};
