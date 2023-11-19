/**
 * WordPress dependencies
 */
import { createContext } from '@wordpress/element';
export const SpaceContext = createContext({
	latestIndex: 0,
});

export const SpaceContextProvider = SpaceContext.Provider;
