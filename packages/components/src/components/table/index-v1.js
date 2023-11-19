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
import { pickBy, identity } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import Input from '../input';
import Result from '../result';
import Empty from '../empty';
import { usePrevious } from '../../hooks';
// import Pagination from '../pagination';
// import Spinner from '../spinner';
// import Toolbar from './toolbar';
// import TableHeader from './header';
// import Body from './body';
import { useQuery, useColumns, useSelection, useExpandable } from './hooks';

// import Empty from '../empty';

function Table( props, ref ) {
	const {
		query = {},
		columns: rawColumns,
		data: rawData,
		totalCount: rawTotalCount,
		loading,
		caption,
		search = true,
		actions,
		renderTools,
		onChange,
		rowKey,
		rowStyle,
		renderExpanded,
		showSummary,
		renderSummary,
		pagination = true,
		emptyMessage,
		errorMessage,
		style,
		className,
		bordered,
	} = props;
	const data = rawData || [];
	const { columns } = useColumns( rawColumns );
	const { isExpanded, onExpandItem } = useExpandable( data );
	const { selectedItems, isSelected, isAllSelected, onSelectItem, onSelectAll } =
		useSelection( data );
	const hasData = data && data.length > 0;
	const showSearch = false !== search;
	const showActions = false !== actions && actions?.length > 0;
	const showToolbar = showSearch || showActions;
	const totalCount = parseInt( rawTotalCount, 10 ) || 0;
	const showPagination = false !== pagination && totalCount > 0;

	// ====================== Methods ======================
	const handleChange = ( newQuery ) => {
		onChange( newQuery );
	};
	const handleSearch = ( keyword ) => {
		handleChange( { ...query, search: keyword, page: 1 } );
		props.onSearch?.( keyword );
	};
	const handleSort = ( { orderby, order } ) => {
		handleChange( { ...query, orderby, order, page: 1 } );
		props.onSort?.( { orderby, order } );
	};
	const handlePageChange = ( page ) => {
		handleChange( { ...query, page } );
		props.onPageChange?.( page );
	};
	const handlePageSizeChange = ( pageSize ) => {
		handleChange( { ...query, page: 1, pageSize } );
		props.onPageSizeChange?.( pageSize );
	};
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

	// ====================== Render ======================
	const renderToolbar = () => {
		if ( ! showToolbar ) {
			return null;
		}
		return (
			<div className="eac-table__section eac-table__section--toolbar">
				{ showSearch && (
					<SearchControl
						className="eac-table__search"
						disabled={ loading }
						value={ query?.search || '' }
						onChange={ handleSearch }
						placeholder={ __( 'Search', 'wp-ever-accounting' ) }
						{ ...( typeof search === 'object' ? search : {} ) }
					/>
				) }
			</div>
		);
	};

	const renderCaption = () => {
		if ( ! caption ) {
			return null;
		}
		return <caption className="eac-table__caption">{ caption }</caption>;
	};

	const renderColGroup = () => {
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
	};

	const renderHeader = () => {
		return (
			<tr>
				{ columns.map( ( column, index ) => {
					let cellNode;
					const Cell = [ 'selectable', 'expandable' ].includes( column.type )
						? 'td'
						: 'th';
					const cellProps = {
						key: column.key || index,
						colSpan: column.colSpan,
						rowSpan: column.rowSpan,
						'aria-colindex': column?.key || index + 1,
						className: classNames( 'eac-table__cell', 'eac-table__cell--header', {
							'eac-table__cell--sortable': column.sortable,
							'eac-table__cell--expandable': column.type === 'expandable',
							'eac-table__cell--selectable': column.type === 'selectable',
							'eac-table__cell--center': column.align === 'center',
							'eac-table__cell--right': column.align === 'right',
							'eac-table__cell--ellipsis': !! column.ellipsis,
							'eac-table__cell--sorted':
								column.sortable && query?.orderby === column.key,
						} ),
						scope: 'col',
						role: 'columnheader',
						...( column.sortable && {
							'aria-sort': query?.orderby === column.key ? query?.order : 'none',
						} ),
					};
					if ( 'selectable' === column.type ) {
						cellNode = (
							<Input.Checkbox
								disabled={ column.disabled || loading }
								onChange={ ( checked ) => onSelectAll( checked ) }
								checked={ isAllSelected }
							/>
						);
					} else {
						cellNode = column.renderHeader?.( column ) || column.title || '';
					}

					return (
						<Cell key={ column.key || index } { ...cellProps }>
							{ column.sortable ? (
								<Button
									className="eac-table__sort-button"
									onClick={ () =>
										handleSort( {
											orderby: column.key,
											order:
												query?.orderby === column.key &&
												query?.order === 'asc'
													? 'desc'
													: 'asc',
										} )
									}
									aria-disabled={ loading }
									disabled={ loading }
								>
									{ cellNode }
									<span className="eac-table__sort-icon">
										<Icon
											icon={
												query?.orderby === column.key &&
												query?.order === 'asc'
													? 'arrow-up-alt2'
													: 'arrow-down-alt2'
											}
											size={ 12 }
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
	};

	const renderBody = () => {
		if ( loading ) {
			return (
				<tr>
					<td
						colSpan={ columns.length }
						className="eac-table__cell eac-table__cell--loading"
					>
						loading
					</td>
				</tr>
			);
		}
		if ( hasData ) {
			return (
				<Fragment>
					{ data.map( ( row, rowIndex ) => {
						return [
							<tr
								key={ getRowKey( row, rowIndex ) }
								style={ getRowStyle( row, rowIndex ) }
								aria-rowindex={ getRowKey( row, rowIndex ) }
							>
								{ columns.map( ( column, columnIndex ) => {
									let cellNode;
									if ( 'selectable' === column.type ) {
										cellNode = (
											<Input.Checkbox
												disabled={ column.disabled || loading }
												onChange={ ( checked ) =>
													onSelectItem( checked, row )
												}
												checked={ isSelected( row ) }
											/>
										);
									} else if ( 'expandable' === column.type ) {
										cellNode = (
											<Button
												className="eac-table__expand-button"
												disabled={ column.disabled || loading }
												onClick={ () => onExpandItem( row ) }
											>
												<Icon
													size={ 16 }
													icon={
														isExpanded( row )
															? 'arrow-down-alt2'
															: 'arrow-right-alt2'
													}
												/>
											</Button>
										);
									} else {
										cellNode = column.render?.(
											row,
											column,
											rowIndex,
											columnIndex
										);
									}
									const cellProps = {
										colSpan: column.colSpan,
										rowSpan: column.rowSpan,
										'aria-colindex': column?.key || columnIndex + 1,
										className: classNames( 'eac-table__cell', {
											'eac-table__cell--expandable':
												column.type === 'expandable',
											'eac-table__cell--selectable':
												column.type === 'selectable',
											'eac-table__cell--center': column.align === 'center',
											'eac-table__cell--right': column.align === 'right',
											'eac-table__cell--ellipsis': !! column.ellipsis,
											'eac-table__cell--sorted':
												column.sortable && query?.orderby === column.key,
										} ),
									};
									return (
										<td key={ column.key || columnIndex } { ...cellProps }>
											{ cellNode }
										</td>
									);
								} ) }
							</tr>,
							isExpanded( row ) && (
								<tr key={ `${ getRowKey( row, rowIndex ) }-expanded` }>
									<td
										className="eac-table__cell eac-table__cell--expanded"
										colSpan={ columns.length }
									>
										{ renderExpanded?.( row, rowIndex ) }
									</td>
								</tr>
							),
						];
					} ) }
					{ showSummary && (
						<tr>
							{ columns.map( ( column, index ) => {
								return (
									<td
										className="eac-table__cell eac-table__cell--summary"
										key={ column.key || index }
										aria-colindex={ column.key || index }
									>
										{ renderSummary?.( column, data, index ) }
									</td>
								);
							} ) }
						</tr>
					) }
				</Fragment>
			);
		}
	};

	const classes = classNames( 'eac-table', className, {
		'eac-table--empty': ! hasData && ! loading,
		'eac-table--bordered': !! bordered,
		'eac-table--loading': !! loading,
	} );

	return (
		<div className={ classes } style={ style } ref={ ref }>
			{ renderToolbar() }
			<div className="eac-table__container">
				<table>
					{ renderCaption() }
					{ renderColGroup() }
					<thead>{ renderHeader() }</thead>
					<tbody>{ renderBody() }</tbody>
				</table>
			</div>
		</div>
	);
}

export default forwardRef( Table );
