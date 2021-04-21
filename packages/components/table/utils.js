/**
 * External dependencies
 */
import { has, get } from 'lodash';

const defaultRender = (row, column) => get(row, [column.property]);

export function normalizeColumns(columns) {
	return columns.map((column) => {
		let { width, align = 'left' } = column;
		if (width !== undefined) {
			width = parseInt(width, 10);
			if (isNaN(width)) {
				width = null;
			}
		}
		return Object.assign(
			{
				sortable: false,
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
				headerAlign: column.headerAlign
					? 'is-' + column.headerAlign
					: column.align
					? 'is-' + column.align
					: null,
			}
		);
	});
}
