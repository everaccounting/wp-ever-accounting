/**
 * Internal dependencies
 */
import { useMergedState } from '../hooks';

function usePagination( onChange, pagination ) {
	const { totalCount = 0, ...paginationProps } = pagination && typeof pagination === 'object' ? pagination : {};

	const [ currentPage, setCurrentPage ] = useMergedState( {
		defaultValue: 1,
		value: pagination?.currentPage || 1,
	} );
	console.log( 'currentPage', currentPage );

	if ( currentPage > 5 ) {
		setCurrentPage( 10 );
	}


	// const { totalCount = 0, currentPage = 1, perPage = 20, ...paginationProps } = pagination || {};
	//
	// const [ currentPageState, setCurrentPage ] = useState( currentPage );
	// const [ perPageState, setPerPage ] = useState( perPage );
	// const mergedState = {
	// 	currentPage: currentPageState,
	// 	perPage: perPageState,
	// 	...paginationProps,
	// };
	//
	// if ( pagination === false ) {
	// 	return {};
	// }
	// const maxPage = Math.ceil( totalCount / perPageState );
	// const start = ( currentPageState - 1 ) * perPageState + 1;
	// const end = Math.min( currentPageState * perPageState, totalCount );
	//
	// const handlePaginationChange = ( page, _perPage ) => {
	// 	paginationProps.onChange?.( page, _perPage );
	// 	setCurrentPage( page );
	// 	setPerPage( _perPage || mergedState.perPage );
	// 	onChange( page, _perPage || mergedState.perPage );
	// };
	//
	// return {
	// 	...mergedState,
	// 	start,
	// 	end,
	// 	maxPage,
	// 	onChange: handlePaginationChange,
	// };
}

export default usePagination;
