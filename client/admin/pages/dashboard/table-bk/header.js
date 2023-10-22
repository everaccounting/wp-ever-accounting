/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

function HeaderRow({ onHeaderRow, cells, index, props }) {
	let rowProps;
	if (onHeaderRow) {
		rowProps = onHeaderRow(
			cells.map((cell) => cell.column),
			index
		);
	}
	return null;
}

function Header(props) {
	const { columns, data, onHeaderRow } = props;

	const parseHeaderRows = (rootColumns) => {
		const rows = [];
		function fillRowCells(cols, colIndex, rowIndex = 0) {
			rows[rowIndex] = rows[rowIndex] || [];
			let currentColIndex = colIndex;
			return cols.filter(Boolean).map((column) => {
				const cell = {
					key: column.key,
					className: column.className || '',
					children: column.title,
					column,
					colStart: currentColIndex,
				};
				let colSpan = 1;
				const subColumns = column.children;
				if (subColumns && subColumns.length > 0) {
					colSpan = fillRowCells(subColumns, currentColIndex, rowIndex + 1).reduce(
						(total, count) => total + count,
						0
					);
					cell.hasSubColumns = true;
				}
				if ('colSpan' in column) {
					({ colSpan } = column);
				}
				if ('rowSpan' in column) {
					cell.rowSpan = column.rowSpan;
				}
				cell.colSpan = colSpan;
				cell.colEnd = cell.colStart + colSpan - 1;
				rows[rowIndex].push(cell);
				currentColIndex += colSpan;
				return colSpan;
			});
		}

		// Generate `rows` cell data
		fillRowCells(rootColumns, 0);
		// Handle `rowSpan`
		const rowCount = rows.length;
		for (let rowIndex = 0; rowIndex < rowCount; rowIndex += 1) {
			rows[rowIndex].forEach((cell) => {
				if (!('rowSpan' in cell) && !cell.hasSubColumns) {
					// eslint-disable-next-line no-param-reassign
					cell.rowSpan = rowCount - rowIndex;
				}
			});
		}
		return rows;
	};
	const rows = useMemo(() => parseHeaderRows(columns), [columns]);
	{
		rows.map((row, rowIndex) => {
			return <HeaderRow key={rowIndex} cells={row} onHeaderRow={onHeaderRow} columns={columns} />;
		});
	}

	return (
		<div>
			{rows.map((row, rowIndex) => {
				return <HeaderRow key={rowIndex} cells={row} onHeaderRow={onHeaderRow} columns={columns} index={rowIndex} />;
			})}
		</div>
	);
}

export default Header;
