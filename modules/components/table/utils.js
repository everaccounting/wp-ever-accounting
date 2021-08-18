/**
 * External dependencies
 */
import { get } from 'lodash';

const defaultRender = (row, column) => get(row, [column.property]);

export function normalizeColumns(columns) {
	return columns.map((column) => {
		let { width } = column;
		if (width !== undefined) {
			width = parseInt(width, 10);
			if (isNaN(width)) {
				width = null;
			}
		}
		return Object.assign(
			{
				sortable: false,
				isPrimary: false,
				enabled: false,
				type: 'content',
				align: 'left',
			},
			column,
			{
				columnKey: column.columnKey || column.property,
				width,
				property: column.property || column.property,
				render: column.render || defaultRender,
				align: column.align ? 'is-' + column.align : null,
				// eslint-disable-next-line no-nested-ternary
				headerAlign: column.headerAlign
					? 'is-' + column.headerAlign
					: column.align
					? 'is-' + column.align
					: null,
				actions: column.actions || [],
			}
		);
	});
}
