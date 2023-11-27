/**
 * External dependencies
 */
import * as React from 'react';
/**
 * Internal dependencies
 */
import { decorateColumns, findColumnIndexById, getSortDirection } from '../DataTable/util';
import useDidUpdateEffect from '../hooks/useDidUpdateEffect';
function useColumns( columns, onColumnOrderChange, defaultSortFieldId, defaultSortAsc ) {
	const [ tableColumns, setTableColumns ] = React.useState( () => decorateColumns( columns ) );
	const [ draggingColumnId, setDraggingColumn ] = React.useState( '' );
	const sourceColumnId = React.useRef( '' );
	useDidUpdateEffect( () => {
		setTableColumns( decorateColumns( columns ) );
	}, [ columns ] );
	const handleDragStart = React.useCallback(
		( e ) => {
			const { attributes } = e.target;
			const id = attributes.getNamedItem( 'data-column-id' )?.value;
			if ( id ) {
				sourceColumnId.current = tableColumns[ findColumnIndexById( tableColumns, id ) ]?.id?.toString() || '';
				setDraggingColumn( sourceColumnId.current );
			}
		},
		[ tableColumns ]
	);
	const handleDragEnter = React.useCallback(
		( e ) => {
			const { attributes } = e.target;
			const id = attributes.getNamedItem( 'data-column-id' )?.value;
			if ( id && sourceColumnId.current && id !== sourceColumnId.current ) {
				const selectedColIndex = findColumnIndexById( tableColumns, sourceColumnId.current );
				const targetColIndex = findColumnIndexById( tableColumns, id );
				const reorderedCols = [ ...tableColumns ];
				reorderedCols[ selectedColIndex ] = tableColumns[ targetColIndex ];
				reorderedCols[ targetColIndex ] = tableColumns[ selectedColIndex ];
				setTableColumns( reorderedCols );
				onColumnOrderChange( reorderedCols );
			}
		},
		[ onColumnOrderChange, tableColumns ]
	);
	const handleDragOver = React.useCallback( ( e ) => {
		e.preventDefault();
	}, [] );
	const handleDragLeave = React.useCallback( ( e ) => {
		e.preventDefault();
	}, [] );
	const handleDragEnd = React.useCallback( ( e ) => {
		e.preventDefault();
		sourceColumnId.current = '';
		setDraggingColumn( '' );
	}, [] );
	const defaultSortDirection = getSortDirection( defaultSortAsc );
	const defaultSortColumn = React.useMemo(
		() => tableColumns[ findColumnIndexById( tableColumns, defaultSortFieldId?.toString() ) ] || {},
		[ defaultSortFieldId, tableColumns ]
	);
	return {
		tableColumns,
		draggingColumnId,
		handleDragStart,
		handleDragEnter,
		handleDragOver,
		handleDragLeave,
		handleDragEnd,
		defaultSortDirection,
		defaultSortColumn,
	};
}
export default useColumns;
