/**
 * WordPress dependencies
 */
import { forwardRef, Fragment, useMemo, useRef, useState } from '@wordpress/element';
import { Button, Tooltip, Icon, SearchControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
/**
 * External dependencies
 */
import classNames from 'classnames';
import PropTypes from 'prop-types';
import { pickBy, identity, debounce } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import Dropdown from '../dropdown';
import Placeholder from '../placeholder';
import Input from '../input';
import Pagination from '../pagination';
import Empty from '../empty';
import { usePagination } from '../pagination';
import { usePrevious, useControlledValue } from '../../hooks';
import { useColumns, useExpandable, useSelection } from './hooks';

// import Empty from '../empty';

function Table(props) {
	const {
		query = {},
		columns = [],
		data = [],
		loading,
		caption,
		search = true,
		actions,
		onChange,
		rowKey,
		rowStyle,
		renderExpanded,
		showSummary,
		renderSummary,
		pagination = true,
		emptyMessage,
		style,
		className,
		bordered = true,
	} = props;

	const { mergedColumns, setMergedColumns } = useColumns(columns);
	const { isExpanded, onExpandItem } = useExpandable(data);
	const { isSelected, isAllSelected, onSelectItem, onSelectAll } = useSelection(data);
	const hasData = data && data.length > 0;
	const showSearch = false !== search;
	const showActions = false !== actions && actions?.length > 0;
	const showToolbar = showSearch || showActions;
	const showPagination = false !== pagination && hasData;
	const [searchWord, setSearchWord] = useState(query?.search || '');
	// ====================== Methods ======================
	const handleChange = (newQuery) => {
		onChange(newQuery);
	};
	const handleSearch = (keyword) => {
		props.onSearch?.(keyword);
		handleChange({ ...query, search: keyword, page: 1 });
	};
	const handleSort = ({ orderby, order }) => {
		props.onSort?.({ orderby, order });
		handleChange({ ...query, orderby, order, page: 1 });
	};
	const handlePagination = (page, perPage) => {
		handleChange({ ...query, page, per_page: perPage });
		props.onPaginate?.({ page, perPage });
	};
	const getRowKey = (row, index) => {
		if (typeof rowKey === 'function') {
			return rowKey(row, index);
		}
		return row[rowKey] || index;
	};
	const getRowStyle = (row, index) => {
		if (typeof rowStyle === 'function') {
			return rowStyle(row, index);
		}
		return rowStyle;
	};

	// ========================== Pagination ==========================
	const mergedPagination = usePagination(data?.length, handlePagination, {
		...query,
		...pagination,
	});

	console.log(mergedPagination);

	// ============================= Render =============================
	const classes = classNames('eac-table', className, {
		'eac-table--empty': !hasData && !loading,
		'eac-table--bordered': !!bordered,
		'eac-table--loading': !!loading,
	});
	return (
		<div className={classes} style={style}>
			{showPagination && (
				<div className="eac-table__section eac-table__section--pagination">
					<Pagination {...mergedPagination} className="eac-table__pagination" />
				</div>
			)}
		</div>
	);
}

Table.propTypes = {
	// Current query object of the table. This object contains the current page, page size, sorting, and filtering information.
	query: PropTypes.object,
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape({
			// Title of this column
			title: PropTypes.string,
			// column's key. If you need to use the onFilterChange event, you need this attribute to identify which column is being filtered.
			key: PropTypes.string,
			// Display field of the data record, support nest path by string array
			dataIndex: PropTypes.string,
			// type of the column. If set to selection, the column will display checkbox. If set to index, the column will display index of the row (staring from 1). If set to expand, the column will display expand icon.
			type: PropTypes.oneOf(['selectable', 'expandable']),
			//alignment of the table cell. If omitted, the value of the above align attribute will be applied.
			align: PropTypes.oneOf(['left', 'center', 'right']),
			//alignment of the table header. If omitted, the value of the above align attribute will be applied.
			headerAlign: PropTypes.oneOf(['left', 'center', 'right']),
			//classname
			className: PropTypes.string,
			//Span of this column's title
			colSpan: PropTypes.number,
			//is sortable or not
			sortable: PropTypes.bool,
			//column width
			width: PropTypes.number,
			//with width has a fixed width, while columns with minWidth
			minWidth: PropTypes.number,
			// Renderer of the table cell. The return value should be a ReactNode.
			render: PropTypes.func,
			//render function for table header of this column
			renderHeader: PropTypes.func,
			//function that determines if a certain row can be selected, works when type is 'selection'
			disabled: PropTypes.oneOfType([PropTypes.func, PropTypes.bool]),
		})
	),
	// Table data. The data is an array of objects.
	data: PropTypes.arrayOf(PropTypes.object),
	// Rendering toolbar supports returning a DOM array and automatically adds margin-right to the last element.
	toolbarRender: PropTypes.oneOfType([PropTypes.func, PropTypes.bool]),
	//Whether to display the search form, when the object is passed in, it is the configuration of the search form.
	search: PropTypes.oneOfType([PropTypes.bool, PropTypes.object]),
	// Bulk actions for the table.
	// bulkActions: PropTypes.arrayOf(
	// 	PropTypes.shape( {
	// 		key: PropTypes.string,
	// 		label: PropTypes.string,
	// 		onClick: PropTypes.func,
	// 	} )
	// ),
};

export default Table;
