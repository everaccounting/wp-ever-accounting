/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

function usePagination( onChange, pagination ) {
	const { totalCount = 0, ...paginationProps } = pagination && typeof pagination === 'object' ? pagination : {};
	const [ state, setState ] = useState( () => ( {
		currentPage: 'currentPage' in paginationProps ? paginationProps.currentPage : 1,
		perPage: 'defaultPerPage' in paginationProps ? paginationProps.defaultPerPage : 20,
	} ) );

	if ( pagination === false ) {
		return {};
	}

	const mergedState = {
		...state,
		...paginationProps,
	};

	const maxPage = Math.ceil( totalCount / mergedState.perPage );
	const currentPage = Math.min( mergedState.currentPage, maxPage ) || 1;
	const start = ( mergedState.currentPage - 1 ) * mergedState.perPage + 1;
	const end = Math.min( mergedState.currentPage * mergedState.perPage, mergedState.totalCount );
	return {
		...mergedState,
		currentPage,
		start,
		end,
		maxPage,
		onChange: ( page, perPage ) => {
			if ( paginationProps ) {
				paginationProps.onChange?.( page, perPage );
			}
			setState( {
				currentPage: page ?? 1,
				perPage: perPage || mergedState.perPage,
			} );
			onChange( page, perPage || mergedState?.perPage );
		},
	};
}

export default usePagination;
