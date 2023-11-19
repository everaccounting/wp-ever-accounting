/**
 * WordPress dependencies
 * @param {Object} columns The columns to normalize.
 */
export function normalizeColumns( columns ) {
	return columns.map( ( column, index ) => {
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
			render: column.render || defaultRender,
			align: column.align ?? null,
			visible: column.visible ?? true,
			headerAlign: column.headerAlign ?? column.align,
			renderHeader: column.renderHeader || null,
		};
	} );
}

export const getValueByPath = ( data, path ) => {
	if ( typeof path !== 'string' ) return null;
	return path.split( '.' ).reduce( ( pre, cur ) => ( pre || {} )[ cur ], data );
};

const defaultRender = ( row, column ) => getValueByPath( row, column.dataIndex );
