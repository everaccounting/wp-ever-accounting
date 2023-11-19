/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { identity, omit, pickBy, uniq, isArray, has, isEqual } from 'lodash';
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useMemo, useState } from '@wordpress/element';
import { SearchControl, Button, Icon } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.scss';
import Placeholder from '../placeholder';
import { normalizeColumns, getValueByPath } from './utils';
import { useControlledValue } from '../utils';

function Table( props ) {
	const { data, isLoading, toolbarRender, search, caption, ...rest } = props;
	const [ query, setQuery ] = useControlledValue( {
		defaultValue: {},
		value: pickBy( props.query, identity ),
		onChange: ( value ) => {
			props.onQueryChange?.( pickBy( value, identity ) );
		},
	} );
	const columns = useMemo( () => normalizeColumns( props.columns ), [ props.columns ] );
	const hasData = useMemo( () => data && data.length > 0, [ data ] );
	const [ expandedRows, setExpandedRows ] = useState( [] );
	const [ selectedRows, setSelectedRows ] = useState( [] );

	// SEARCH NODE.
	const searchNode = useMemo( () => {
		// if renderToolbar is falsy, we don't render the search form otherwise initialize with default props.
		if ( ! toolbarRender || ! search ) {
			return null;
		}
		return (
			<SearchControl
				className="eac-table__search"
				value={ query?.search ?? '' }
				onChange={ ( value ) => {
					setQuery( { ...query, search: value } );
				} }
				{ ...( typeof search === 'object' ? search : {} ) }
			/>
		);
	}, [ query, search, setQuery, toolbarRender ] );

	// BULK ACTIONS NODE.
	const bulkActionsNode = useMemo( () => {
		if ( ! toolbarRender || ! props.bulkActions || ! props.bulkActions.length ) {
			return null;
		}
		return (
			<div className="eac-table__bulk-actions">
				<Button isLink>{ __( 'Bulk Actions' ) }</Button>
				{ props.bulkActions }
			</div>
		);
	}, [ props.bulkActions, toolbarRender ] );

	// TOOLBAR NODE.
	const toolbarNode = useMemo( () => {
		if ( ! toolbarRender ) {
			return null;
		}
		return (
			<div className="eac-table__section eac-table__section--toolbar">
				{ bulkActionsNode }
				{ searchNode }
				{ toolbarRender }
			</div>
		);
	}, [ bulkActionsNode, searchNode, toolbarRender ] );

	// TABLE HEADER NODE.
	const tableHeaderNode = useMemo( () => {
		return (
			<tr>
				{ columns.map( ( column, index ) => {
					const Cell = column.type === 'selection' ? 'td' : 'th';
					const classes = classNames( 'eac-table__column', column.className, {
						'eac-table__cell': true,
						'eac-table__column--sortable': column.sortable,
						'eac-table__cell--selection': column.type === 'selection',
						'eac-table__cell--expandable': column.type === 'expandable',
						'eac-table__cell--center': 'center' === column.headerAlign,
						'eac-table__cell--right': 'right' === column.headerAlign,
					} );
					const cellNode = column.renderHeader ? column.renderHeader( column ) : column.title;

					return (
						<Cell
							key={ column.key || index }
							colSpan={ column.colSpan }
							rowSpan={ column.rowSpan }
							className={ classes }
							scope="col"
							role="columnheader"
						>
							{ column.sortable ? (
								<Button
									className="eac-table__sort-button"
									// onClick={ () =>
									// 	onSort( {
									// 		orderby: column.key,
									// 		order:
									// 			query?.orderby === column.key && query?.order === 'asc'
									// 				? 'desc'
									// 				: 'asc',
									// 	} )
									// }
									aria-label={ sprintf(
										/* translators: %s: column label */
										__( 'Sort by %s', 'wp-ever-accounting' ),
										column.title
									) }
									aria-disabled={ isLoading }
									disabled={ isLoading }
								>
									{ cellNode }
									<span className="eac-table__sort-icon">
										<Icon
											icon={
												query?.orderby === column.key && query?.order === 'asc'
													? 'arrow-up-alt2'
													: 'arrow-down-alt2'
											}
											size={ 16 }
										/>
									</span>
								</Button>
							) : (
								cellNode
							) }
						</Cell>
					);
				} ) }
			</tr>
		);
	}, [ columns, isLoading, query?.order, query?.orderby ] );

	return (
		<div className="eac-table">
			{ toolbarNode }
			<div className="eac-table-container">
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
					<thead>{ tableHeaderNode }</thead>
					<tbody>
						{ ! hasData && ! isLoading && (
							<tr>
								<td className="eac-table__cell eac-table__cell--empty" colSpan={ columns.length }>
									{ props.emptyText }
								</td>
							</tr>
						) }
						{ isLoading &&
							Array.from( { length: 10 }, ( _, index ) => (
								<tr key={ index }>
									{ columns.map( ( column, i ) => (
										<td key={ i } className="eac-table__cell eac-table__cell--loading">
											<Placeholder.Button />
										</td>
									) ) }
								</tr>
							) ) }
					</tbody>
				</table>
			</div>
		</div>
	);
}

Table.propTypes = {
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
			dataIndex: PropTypes.string,
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
			disabled: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
		} )
	),
	// Table data. The data is an array of objects.
	data: PropTypes.arrayOf( PropTypes.object ),
	// Rendering toolbar supports returning a DOM array and automatically adds margin-right to the last element.
	toolbarRender: PropTypes.oneOfType( [ PropTypes.func, PropTypes.bool ] ),
	//Whether to display the search form, when the object is passed in, it is the configuration of the search form.
	search: PropTypes.oneOfType( [ PropTypes.bool, PropTypes.object ] ),
	// Bulk actions for the table.
	// bulkActions: PropTypes.arrayOf(
	// 	PropTypes.shape( {
	// 		key: PropTypes.string,
	// 		label: PropTypes.string,
	// 		onClick: PropTypes.func,
	// 	} )
	// ),
};

Table.defaultProps = {
	query: {
		page: 1,
		pageSize: 10,
		sort: {},
		search: '',
	},
	columns: [],
	data: [],
	toolbarRender: true,
	search: true,
	bulkActions: [],
};

export default Table;
