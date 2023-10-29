/**
 * WordPress dependencies
 */
import { Fragment, forwardRef, useMemo, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { noop } from 'lodash';
import classNames from 'classnames';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import usePagination from '../pagination/use-pagination';
import Placeholder from '../placeholder';
import TableHeader from './table-header';
import TableRows from './table-rows';
import TableSummery from './table-summery';
import { normalizeColumns } from './utils';
// import './style.scss';
import Element from '../placeholder/element';
import Button from '../placeholder/button';

function Table( props ) {
	const { className, columns: _columns, dataSource = [], showSummary, caption, ...restProps } = props;
	const columns = useMemo( () => normalizeColumns( _columns ), [ _columns ] );
	const hasData = useMemo( () => dataSource && dataSource.length > 0, [ dataSource ] );
	const [ expandedRows, setExpandedRows ] = useState( [] );
	const [ selectedRows, setSelectedRows ] = useState( [] );
	const isAllSelected = useMemo( () => selectedRows.length === dataSource.length, [ selectedRows, dataSource ] );
	const onToggleExpanded = ( row ) => {
		const expanded = expandedRows.includes( row ) ? expandedRows.filter( ( r ) => r !== row ) : [ ...expandedRows, row ];
		setExpandedRows( expanded );
		dispatchEvent( 'onExpand', expanded );
	};
	const onSelectAll = ( checked ) => {
		setSelectedRows( checked ? dataSource : [] );
		dispatchEvent( 'onSelectAll', checked );
		dispatchEvent( 'onSelectChange', checked ? dataSource : [] );
	};
	const onSelectChange = ( checked, row ) => {
		const selected = selectedRows.includes( row ) ? selectedRows.filter( ( r ) => r !== row ) : [ ...selectedRows, row ];
		setSelectedRows( selected );
		dispatchEvent( 'onSelectChange', selected );
	};

	const dispatchEvent = ( name, ...args ) => ( props[ name ] || noop )( ...args );

	const classes = classNames( 'eac-table', className, {
		'eac-table--empty': ! hasData,
		'eac-table--bordered': !! props.bordered,
		'eac-table--striped': !! props.striped,
		'eac-table--loading': !! props.loading,
	} );

	return (
		<Fragment>
			<div className={ classes } role="group">
				<table>
					{ caption && <caption className="eac-table__caption">{ caption }</caption> }
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
					<thead>
						<TableHeader { ...restProps } columns={ columns } isAllSelected={ isAllSelected } onSelectAll={ onSelectAll } />
					</thead>
					<tbody>
						{ hasData ? (
							<TableRows
								{ ...restProps }
								columns={ columns }
								dataSource={ dataSource }
								selectedRows={ selectedRows }
								onSelectChange={ onSelectChange }
								onToggleExpanded={ onToggleExpanded }
							/>
						) : (
							<tr>
								<td className="eac-table__cell eac-table__cell--empty" colSpan={ columns.length }>
									{ props.emptyText }
								</td>
							</tr>
						) }
						{ props.loading &&
							Array.from( { length: 10 }, ( _, index ) => (
								<tr key={ index }>
									{ columns.map( ( column, i ) => (
										<td key={ i } className="eac-table__cell eac-table__cell--loading">
											Loading
										</td>
									) ) }
								</tr>
							) ) }
						{ showSummary && <TableSummery { ...restProps } columns={ columns } dataSource={ dataSource } /> }
					</tbody>
				</table>
			</div>
		</Fragment>
	);
}

Table.propTypes = {
	// An array of columns, as objects.
	columns: PropTypes.arrayOf(
		PropTypes.shape( {
			// Title of this column
			title: PropTypes.string,
			// column's key. If you need to use the onFilterChange event, you need this attribute to identify which column is being filtered.
			key: PropTypes.string,
			// Display field of the data record, support nest path by string array
			property: PropTypes.oneOfType( [ PropTypes.string, PropTypes.arrayOf( PropTypes.string ) ] ),
			// type of the column. If set to selection, the column will display checkbox. If set to index, the column will display index of the row (staring from 1). If set to expand, the column will display expand icon.
			type: PropTypes.oneOf( [ 'selection', 'expandable' ] ),
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
			selectable: PropTypes.func,
			//whether to display a filter icon in the column header. Click the icon to display a list of filtering options.
			filterable: PropTypes.bool,
			//an array of data filtering options. For each element in this array, text and value are required.
			filters: PropTypes.arrayOf(
				PropTypes.shape( {
					//display text of the option
					text: PropTypes.string,
					//value of the option
					value: PropTypes.string,
				} )
			),
			//Customized filter icon.
			filterIcon: PropTypes.func,
			//whether data filtering supports multiple options.
			filterMultiple: PropTypes.bool,
			//data filtering method. If filterMultiple is on, this method will be called multiple times for each row, and a row will display if one of the calls returns true.
			filterMethod: PropTypes.func,
		} )
	),
	// Data record array to be displayed.
	dataSource: PropTypes.arrayOf( PropTypes.object ),
	//Row's unique key, could be a string or function that returns a string.
	rowKey: PropTypes.oneOfType( [ PropTypes.string, PropTypes.func ] ),
	//function that returns custom style for a row, or a string assigning custom style for every row.
	rowStyle: PropTypes.oneOfType( [ PropTypes.func, PropTypes.string ] ),
	//function that returns custom style for a row, or a string assigning custom style for every row.
	rowClassName: PropTypes.oneOfType( [ PropTypes.func, PropTypes.string ] ),
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
	// whether to display a summary row.
	showSummary: PropTypes.bool,
	//displayed text for the first column of summary row/.
	summaryText: PropTypes.string,
	//custom summary method.
	renderSummary: PropTypes.func,
	//Whether to show all table borders.
	bordered: PropTypes.bool,
	//Loading status of table.
	loading: PropTypes.bool,
};

Table.defaultProps = {
	emptyText: __( 'No Data', 'wp-ever-accounting' ),
	loading: false,
	columns: [],
	dataSource: [],
	query: {},
	showSummary: false,
	renderSummary: () => {},
	pagination: false,
	onHeaderRow: noop,
	onSort: noop,
	onFilterChange: noop,
	onExpand: noop,
	onSelect: noop,
};

export default Table;
