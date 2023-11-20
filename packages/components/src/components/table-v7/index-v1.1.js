/**
 * External dependencies
 */
import classNames from 'classnames';
import { findIndex, isEqual, uniq, noop } from 'lodash';

/**
 * WordPress dependencies
 */
import { useMemo, useState, Fragment, memo } from '@wordpress/element';
import { SearchControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useControlledValue, useControlledState, usePrevious } from '../utils';
import { useColumns, useSelection, useExpandable } from './hooks';
import { Pagination, usePagination } from '../pagination';
import Column from './column';
import Cell from './cell';
import DropdownMenu from '../dropdown-menu';
import './style.scss';
import PropTypes from 'prop-types';
import Empty from '../empty';

function Table( props ) {
	const {
		headerTitle,
		headerActions,
		query: _query,
		columns: _columns,
		totalCount: _totalCount,
		data: _data = [],
		bulkActions,
		loading,
		search,
		emptyMessage,
		rowKey,
		rowStyle,
		toolbarRender,
		onChange,
		// onExpand,
		renderExpanded,
		showSummary,
		summaryText,
		renderSummary,
		style,
		className,
		bordered,
	} = props;
	const [ query, setQuery ] = useControlledValue( {
		defaultValue: {},
		value: _query,
		onChange: onChange || noop,
	} );
	const previousData = usePrevious( _data );
	const data = loading ? previousData : _data;
	const { columns } = useColumns( _columns );
	const { isExpanded, onExpandItem } = useExpandable( data );
	const { isSelected, isAllSelected, onSelectItem, onSelectAll } = useSelection( data );
	const totalCount = parseInt( _totalCount, 10 ) || 0;
	const hasData = data && data.length > 0;
	const paginationData = usePagination( {
		totalCount,
		defaultPerPage: query.perPage || 20,
		onChange: ( page, perPage ) => {
			setQuery( { ...query, page, perPage } );
		},
	} );
	// ====================== Methods ======================
	const getRowKey = ( row, index ) => {
		if ( typeof rowKey === 'function' ) {
			return rowKey( row, index );
		}
		return row[ rowKey ] || index;
	};
	const getRowStyle = ( row, index ) => {
		if ( typeof rowStyle === 'function' ) {
			return rowStyle( row, index );
		}
		return rowStyle;
	};
	const setSort = ( { orderby, order } ) => {
		setQuery( { ...query, orderby, order, page: 1 } );
	};
	// ====================== Render ======================
	const headerSection = useMemo( () => {
		if ( ! headerTitle && ! headerActions ) {
			return null;
		}
		return (
			<div className="eac-table__section eac-table__section--header">
				{ headerTitle && (
					<div className="eac-table__section-col eac-table__section-col--left">{ headerTitle }</div>
				) }
				{ headerActions && (
					<div className="eac-table__section-col eac-table__section-col--right">{ headerActions }</div>
				) }
			</div>
		);
	}, [ headerTitle, headerActions ] );
	// filters.

	// Caption.
	const tableCaption = useMemo( () => {
		return props.caption ? <caption className="eac-table__caption">{ props.caption }</caption> : null;
	}, [ props.caption ] );
	// Colgroup.
	const tableColGroups = useMemo( () => {
		return (
			<colgroup>
				{ columns.map( ( column, index ) => (
					<col
						width={ column.width ? column.width : null }
						style={ {
							minWidth: column.minWidth ? column.minWidth : null,
							maxWidth: column.maxWidth ? column.maxWidth : null,
							width: column.width ? column.width : null,
						} }
						key={ index }
					/>
				) ) }
			</colgroup>
		);
	}, [ columns ] );

	// const pagination = usePagination( onPaginationChange, {
	// 	...props.pagination,
	// 	currentPage: query.page || 1,
	// 	perPage: query.perPage || 20,
	// 	totalCount,
	// } );
	// console.log( 'pagination', pagination );

	// Table header.
	const tableHeader = (
		<thead>
			<tr>
				{ columns.map( ( column, index ) => (
					<Column
						index={ index }
						key={ column.key }
						loading={ loading }
						column={ column }
						onSort={ setSort }
						query={ query }
						isAllSelected={ isAllSelected }
						onSelectAll={ onSelectAll }
					/>
				) ) }
			</tr>
		</thead>
	);

	const tableBody = (
		<tbody>
			{ data.length ? (
				<Fragment>
					{ data.map( ( row, index ) => {
						const key = getRowKey( row, index );
						return [
							<tr key={ key } style={ getRowStyle( row, index ) }>
								{ columns.map( ( column, i ) => (
									<Cell
										key={ i }
										index={ i }
										query={ query }
										column={ column }
										row={ row }
										isSelected={ isSelected( row ) }
										onSelectRow={ onSelectItem }
										isExpanded={ isExpanded( row ) }
										onRowExpanded={ onExpandItem }
									/>
								) ) }
							</tr>,
							isExpanded( row ) && (
								<tr key={ key + '-expanded' }>
									<td
										colSpan={ columns.length }
										className="eac-table__cell eac-table__cell--expanded"
									>
										{ typeof renderExpanded === 'function' && renderExpanded( row ) }
									</td>
								</tr>
							),
						];
					} ) }
				</Fragment>
			) : (
				<tr>
					<td className="eac-table__cell eac-table__cell--empty" colSpan={ columns.length }>
						{ emptyMessage || <Empty description={ __( 'No items found', 'wp-ever-accounting' ) } /> }
					</td>
				</tr>
			) }
		</tbody>
	);

	const tableSummary = useMemo( () => {
		if ( ! showSummary ) {
			return null;
		}
		return (
			<tr>
				{ columns.map( ( column, index ) => (
					<td key={ `summary-${ column.key || index }` }>{ summaryText }</td>
				) ) }
			</tr>
		);
	}, [ showSummary, columns, summaryText ] );

	const classes = classNames( 'eac-table', props.className, {
		'eac-table--empty': ! hasData,
		'eac-table--bordered': !! bordered,
		'eac-table--loading': !! loading,
	} );

	return (
		<div className={ classes } style={ props.style }>
			{ headerSection }
			<div className="eac-table__container">
				<table style={ props.tableStyle } className="eac-table__table">
					{ tableCaption }
					{ tableColGroups }
					{ tableHeader }
					{ tableBody }
					{ tableSummary }
				</table>
			</div>

			<Pagination { ...paginationData } />
		</div>
	);
}

Table.propTypes = {
	// Table  Header title.
	headerTitle: PropTypes.oneOfType( [ PropTypes.node, PropTypes.bool ] ),
	// Table  Header actions.
	headerActions: PropTypes.oneOfType( [ PropTypes.node, PropTypes.bool ] ),
	// Current query object of the table. This object contains the current page, page size, sorting, and filtering information.
	query: PropTypes.object,
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape( {
			// Title of this column
			title: PropTypes.string,
			// column's key. If you need to use the onFilterChange event, you need this attribute to identify which column is being filtered.
			key: PropTypes.string,
			// Display field of the data record, support nest path by string array
			dataIndex: PropTypes.oneOfType( [ PropTypes.string, PropTypes.arrayOf( PropTypes.string ) ] ),
			// type of the column. If set to selection, the column will display checkbox. If set to index, the column will display index of the row (staring from 1). If set to expand, the column will display expand icon.
			type: PropTypes.oneOf( [ 'selectable', 'expandable' ] ),
			//alignment of the table cell. If omitted, the value of the above align attribute will be applied.
			align: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
			//alignment of the table header. If omitted, the value of the above align attribute will be applied.
			headerAlign: PropTypes.oneOf( [ 'left', 'center', 'right' ] ),
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
			disabled: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
		} )
	),
	totalCount: PropTypes.oneOfType( [ PropTypes.number, PropTypes.string ] ),
	// Table data. The data is an array of objects.
	data: PropTypes.arrayOf( PropTypes.object ),
	// Bulk actions to be displayed in the table header.
	bulkActions: PropTypes.arrayOf( PropTypes.object ),
	// Default sorting column key and order. The order of the sorting can be 'ascend' or 'descend'.
	defaultSort: PropTypes.shape( {
		key: PropTypes.string,
		order: PropTypes.oneOf( [ 'ascend', 'descend' ] ),
	} ),
	// Default per page size.
	defaultPerPage: PropTypes.number,
	// Selected rows.
	selectedRows: PropTypes.arrayOf( PropTypes.object ),
	// Expanded rows.
	expandedRows: PropTypes.arrayOf( PropTypes.object ),
	//Loading status of table.
	loading: PropTypes.bool,
	// Content to be displayed when there is no data.
	emptyMessage: PropTypes.oneOfType( [ PropTypes.node, PropTypes.string ] ),
	//Row's unique key, could be a string or function that returns a string.
	rowKey: PropTypes.oneOfType( [ PropTypes.string, PropTypes.func ] ),
	//function that returns custom style for a row, or a string assigning custom style for every row.
	rowStyle: PropTypes.oneOfType( [ PropTypes.func, PropTypes.string ] ),
	// Whether to display the toolbar, when the object is passed in, it is the configuration of the toolbar.
	toolbar: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.object ] ),
	// Rendering toolbar supports returning a DOM array and automatically adds margin-right to the last element.
	toolbarRender: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
	// Triggers when Table's query changes.
	onChange: PropTypes.func,
	//triggers when Table's sorting changes.
	onSort: PropTypes.func,
	//used in multiple selection Table, toggle if a certain row is selected. With the second parameter, you can directly set if this row is selected
	onSelectChange: PropTypes.func,
	//triggers when user expands or collapses a row.
	onExpand: PropTypes.func,
	//Render expanded row.
	renderExpanded: PropTypes.func,
	// whether to display a summary row.
	showSummary: PropTypes.bool,
	//displayed text for the first column of summary row/.
	summaryText: PropTypes.string,
	//custom summary method.
	renderSummary: PropTypes.func,
	//Pagination config, hide pagination when set to false.
	pagination: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.object ] ),
	//Whether to show all table borders.
	bordered: PropTypes.bool,
};

Table.defaultProps = {
	query: {
		page: 1,
		perPage: 10,
		sort: {},
		search: '',
	},
	columns: [],
	data: [],
	search: false,
	showSummary: false,
	renderSummary: () => {},
	pagination: false,
	onSort: noop,
	onExpand: noop,
	onSelect: noop,
};

export default memo( Table );
