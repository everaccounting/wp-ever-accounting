/**
 * WordPress dependencies
 */
import { Button, Icon, CheckboxControl } from '@wordpress/components';
import { useEffect, useMemo, useRef, useState, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classNames from 'classnames';
// eslint-disable-next-line import/no-extraneous-dependencies
import { get, noop } from 'lodash';
// eslint-disable-next-line import/no-extraneous-dependencies
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import './style.scss';
import TableHeader from './header';

const getValueByPath = (data, path) => {
	if (typeof path !== 'string') return null;
	return path.split('.').reduce((pre, cur) => (pre || {})[cur], data);
};
const defaultRender = (row, column) => {
	return getValueByPath(row, column.property);
};

function Table(props) {
	const {
		className,
		data: data,
		columns: _columns,
		loading = false,
		bordered = false,
		caption,
		rowKey = 'id',
		showSummary = false,
		summaryText,
		summaryMethod,
		emptyText = __('No Data', 'wp-ever-accounting'),
		rowClassName,
		rowStyle,
		defaultSort,
		onSort,
		onSelectChange,
		onExpand,
		renderExpanded,
		query = {},
		...restProps
	} = props;
	const container = useRef(null);
	const [expandedRows, setExpandedRows] = useState([]);
	const [selected, setSelected] = useState([]);
	const hasData = useMemo(() => data && data.length > 0, [data]);
	const { orderby, order } = query;
	const sort = { orderby, order };
	const columns = useMemo(() => {
		return _columns.map((column, index) => {
			const align = column.align ? 'is--' + column.align : null;
			return {
				sortable: false,
				...column,
				key: column.key || index,
				width: column?.width || null,
				minWidth: column?.minWidth || null,
				property: column.property || column.key,
				render: column.render || defaultRender,
				align: align,
				headerAlign: column.headerAlign ? 'is--' + column.headerAlign : align,
				renderHeader: column.renderHeader || null,
			};
		});
	}, [_columns]);
	const dispatchEvent = (name, ...args) => (props[name] || noop)(...args);
	const classes = classNames('eac-table', className);
	return (
		<div className={classes} ref={container} role="group">
			<table className="eac-table__table">
				{caption && <caption className="eac-table__caption">{caption}</caption>}
				<colgroup>
					{columns.map((column, index) => (
						<col
							width={column.width ? column.width : null}
							style={{
								minWidth: column.minWidth ? column.minWidth : null,
								maxWidth: column.maxWidth ? column.maxWidth : null,
								width: column.width ? column.width : null,
							}}
							key={index}
						/>
					))}
				</colgroup>
				<thead>
					<TableHeader
						columns={columns}
						sort={sort}
						onSort={handleSort}
						isAllSelected={isAllSelected}
						isRequesting={isRequesting}
						onSelectAll={handleSelectAll}
					/>
				</thead>
			</table>
		</div>
	);
}

Table.propTypes = {
	// Data record array to be displayed.
	data: PropTypes.arrayOf(PropTypes.object),
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape({
			// type of the column. If set to selection, the column will display checkbox. If set to index, the column will display index of the row (staring from 1). If set to expand, the column will display expand icon.
			type: PropTypes.oneOf(['selection', 'index', 'expandable']),
			//alignment of the table cell. If omitted, the value of the above align attribute will be applied.
			align: PropTypes.oneOf(['left', 'center', 'right']),
			//alignment of the table header. If omitted, the value of the above align attribute will be applied.
			headerAlign: PropTypes.oneOf(['left', 'center', 'right']),
			//classname
			className: PropTypes.string,
			//Span of this column's title
			colSpan: PropTypes.number,
			// column's key. If you need to use the onFilterChange event, you need this attribute to identify which column is being filtered.
			key: PropTypes.string,
			// Display field of the data record, support nest path by string array
			property: PropTypes.oneOfType([PropTypes.string, PropTypes.arrayOf(PropTypes.string)]),
			//is sortable or not
			sortable: PropTypes.bool,
			// Title of this column
			title: PropTypes.string,
			//column width
			width: PropTypes.number,
			//with width has a fixed width, while columns with minWidth
			minWidth: PropTypes.number,
			// Renderer of the table cell. The return value should be a ReactNode.
			render: PropTypes.func,
			//render function for table header of this column
			renderHeader: PropTypes.func,
			//function that determines if a certain row can be selected, works when type is 'selection'
			selectable: PropTypes.func,
			//an array of data filtering options. For each element in this array, text and value are required.
			filters: PropTypes.arrayOf(
				PropTypes.shape({
					//display text of the option
					text: PropTypes.string,
					//value of the option
					value: PropTypes.string,
				})
			),
			//Customized filter icon.
			filterIcon: PropTypes.func,
			//whether data filtering supports multiple options.
			filterMultiple: PropTypes.bool,
			//data filtering method. If filterMultiple is on, this method will be called multiple times for each row, and a row will display if one of the calls returns true.
			filterMethod: PropTypes.func,
		})
	),
	//Whether to show all table borders.
	bordered: PropTypes.bool,
	//Loading status of table.
	loading: PropTypes.bool,
	// Caption of table.
	caption: PropTypes.string,
	//Row's unique key, could be a string or function that returns a string.
	rowKey: PropTypes.oneOfType([PropTypes.string, PropTypes.func]),
	// whether to display a summary row.
	showSummary: PropTypes.bool,
	//displayed text for the first column of summary row/.
	summaryText: PropTypes.string,
	//custom summary method.
	summaryMethod: PropTypes.func,
	//Displayed text when data is empty.
	emptyText: PropTypes.string,
	//function that returns custom style for a row, or a string assigning custom style for every row.
	rowClassName: PropTypes.oneOfType([PropTypes.func, PropTypes.string]),
	//function that returns custom style for a row, or a string assigning custom style for every row.
	rowStyle: PropTypes.oneOfType([PropTypes.func, PropTypes.string]),
	//set the default sort column and order. property prop is used to set default sort column, property order is used to set default sort order.
	defaultSort: PropTypes.shape({
		//default sort column
		property: PropTypes.string,
		//default sort order
		order: PropTypes.oneOf(['asc', 'desc']),
	}),
	//triggers when Table's sorting changes.
	onSort: PropTypes.func,
	//used in multiple selection Table, toggle if a certain row is selected. With the second parameter, you can directly set if this row is selected
	onSelectChange: PropTypes.func,
	//triggers when user expands or collapses a row.
	onExpand: PropTypes.func,
	//Render expanded row.
	renderExpanded: PropTypes.func,
	// Current query string represented in object form.
	query: PropTypes.object,
};

export default Table;
