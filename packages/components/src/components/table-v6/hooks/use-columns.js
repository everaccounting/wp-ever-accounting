/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

/**
 * External dependencies
 */

function useColumns( props ) {
	return useMemo( () => {
		const _columns = props.columns.map( ( column, index ) => {
			return {
				...column,
				key: column.key || index,
				dataIndex: column.dataIndex || column.key,
				title: column.title || null,
				tooltip: column.tooltip || null,
				ellipsis: column.ellipsis || null,
				sortable: column.sortable || false,
				expandable: column.expandable || false,
				selectable: column.selectable || false,
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				render: column.render || null,
				align: column.align ?? null,
				visible: column.visible ?? true,
				headerAlign: column.headerAlign ?? column.align,
				renderHeader: column.renderHeader || null,
			};
		} );

		if ( props.selectable ) {
			_columns.unshift( {
				width: 50,
				align: 'center',
				selectable: true,
				...( typeof props.selectable === 'object' ? props.selectable : {} ),
			} );
		}

		if ( props.expandable ) {
			_columns.unshift( {
				width: 50,
				align: 'center',
				expandable: true,
			} );
		}

		return _columns;
	}, [ props.columns, props.selectable, props.expandable ] );
}

export default useColumns;
