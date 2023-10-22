export const normalizeColumns = (columns) => {
	return columns.map((column) => {
		const _column = Object.assign(
			{
				sortable: false,
				align: 'left',
				filterMultiple: true,
			},
			column,
			{
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				property: column.prop || column.property,
				render: column.render || defaultRender,
				align: column.align ? 'is--' + column.align : null,
				headerAlign: column.headerAlign ? 'is--' + column.headerAlign : column.align,
				filterable: column.filters && column.filterMethod,
				filterOpened: false,
				filteredValue: column.filteredValue || null,
				filterPlacement: column.filterPlacement || 'bottom',
				selectable: column.selectable || false,
			}
		);
		_column.headerAlign = _column.headerAlign || _column.align;
		return _column;
	});
};

export const getValueByPath = (data, path) => {
	if (typeof path !== 'string') return null;
	return path.split('.').reduce((pre, cur) => (pre || {})[cur], data);
};

function defaultRender(row, column) {
	return getValueByPath(row, column.property);
}
