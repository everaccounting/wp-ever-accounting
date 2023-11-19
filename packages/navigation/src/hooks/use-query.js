/**
 * WordPress dependencies
 */
import { useCallback } from '@wordpress/element';
/**
 * External dependencies
 */
import { parse } from 'qs';
import { pickBy, identity } from 'lodash';
import { getHistory } from "../history";

function useQuery() {
	const history = getHistory();
	console.log(history);
	// const { path, search } = location;
	//
	// const getQuery = useCallback( () => {
	// 	if ( search.length ) {
	// 		return parse( search.substring( 1 ) ) || {};
	// 	}
	// 	return {};
	// }, [ search ] );
	//
	// const setQuery = useCallback(
	// 	( query, currentQuery = getQuery() ) => {
	// 		return pickBy( { ...currentQuery, ...query }, identity );
	// 	},
	// 	[ getQuery ]
	// );
	//
	// return [ getQuery, setQuery ];
}

export default useQuery;
