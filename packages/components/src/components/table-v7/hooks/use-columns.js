/**
 * WordPress dependencies
 */
import { useMemo, useState } from '@wordpress/element';

/**
 * External dependencies
 */

function useColumns( defaultColumns = [] ) {
	const [ internalColumns, setInternalColumns ] = useState( defaultColumns );
	const columns = useMemo( () => {
		return internalColumns
			.map( ( column, index ) => {
				return {
					type: column.type || null,
					key: column.key || index,
					dataIndex: column.dataIndex || column.key,
					title: column.title || null,
					tooltip: column.tooltip || null,
					ellipsis: column.ellipsis || true,
					sortable: column.sortable || false,
					width: column?.width || null,
					minWidth: column?.minWidth || null,
					render: column.render || null,
					align: column.align ?? null,
					visible: column.visible ?? true,
					headerAlign: column.headerAlign ?? column.align,
					renderHeader: column.renderHeader || null,
					...( [ 'selectable', 'expandable' ].includes( column.type ) && { width: 50 } ),
					...column,
				};
			} )
			.filter( ( column ) => column.visible );
	}, [ internalColumns ] );
	const updateColumn = ( key, column ) => {
		const updatedColumn = {
			...columns.find( ( col ) => col.key === key ),
			...column,
		};
		const newColumns = columns.map( ( col ) => {
			if ( col.key === key ) {
				return updatedColumn;
			}
			return col;
		} );
		setInternalColumns( newColumns );
	};

	return { columns, updateColumn };
}

export default useColumns;
