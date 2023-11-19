/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

function usePagination( { total, onChange, pagination } ) {
	const [ currentPage, setCurrentPage ] = useState( () => Number( pagination?.page ) || 1 );
	const [ perPage, setPerPage ] = useState( () => Number( pagination?.perPage ) || 20 );

	const handlePagination = ( nextPage, nextPerPage ) => {
		if ( perPage !== nextPerPage ) {
			nextPage = 1;
		}

		if ( pagination ) {
			pagination.onChange?.( nextPage, nextPerPage );
		}
		setCurrentPage( nextPage );
		setPerPage( nextPerPage );
		onChange( nextPage, nextPerPage );
	};

	if ( pagination === false ) {
		return {};
	}

	const computedTotal = Number( pagination?.total || total ) || 0;
	const pageCount = Math.ceil( computedTotal / perPage );
	return {
		page: currentPage,
		perPage,
		pageCount,
		total: computedTotal,
		onChange: handlePagination,
	};
}

export default usePagination;

