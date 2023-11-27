/**
 * WordPress dependencies
 */
import { forwardRef, Fragment, useMemo, useRef } from '@wordpress/element';

/**
 * External dependencies
 */
import classNames from 'classnames';
import { pickBy, identity } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import { usePrevious } from '../../hooks';
import Pagination from '../pagination';
import Spinner from '../spinner';
import Toolbar from './toolbar';
import { useQuery, useColumns, useSelection, useExpandable } from './hooks';
import Column from '../table-v7/column';
import Empty from '../empty';
import Cell from '../table-v7/cell';

function Table( props, ref ) {
	const {
		query,
		columns: rawColumns,
		data: rawData,
		totalCount: rawTotalCount,
		status,
		caption,
		emptyMessage,
		errorMessage,
		search = true,
		actions,
		toolbar = true,
		onChange,
		rowKey,
		rowStyle,
		renderExpanded,
		showSummary,
		summaryText,
		renderSummary,
		pagination = true,
		style,
		className,
		bordered,
	} = props;
	const { columns } = useColumns( rawColumns );
	const data = usePrevious( rawData );
	const { isExpanded, onExpandItem } = useExpandable( data );
	const { selectedItems, isSelected, isAllSelected, onSelectItem, onSelectAll } = useSelection( data );
	const totalCount = usePrevious( rawTotalCount );
	const hasData = data && data.length > 0;
	const isLoading = status === 'resolving';
	const hasError = status === 'error';

	// ====================== Methods ======================
	const setChange = ( newQuery ) => {
		onChange( pickBy( newQuery, identity ) );
	};
	const setSearch = ( keyword ) => {
		setChange( { ...query, search: keyword, page: 1 } );
		props.onSearch?.( keyword );
	};
	const setSort = ( { orderby, order } ) => {
		setChange( { ...query, orderby, order, page: 1 } );
		props.onSort?.( { orderby, order } );
	};
	const setPagination = ( { page, per_page } ) => {
		setChange( { ...query, page, per_page } );
		props.onPagination?.( { page, per_page } );
	};
	// ====================== Render ======================
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

	const classes = classNames( 'eac-table', className, {
		'eac-table--empty': ! hasData && ! isLoading,
		'eac-table--bordered': !! bordered,
		'eac-table--loading': !! isLoading,
	} );

	return (
		<div className={ classes } style={ style } ref={ ref }>
			{ toolbar && <Toolbar { ...props } onSearch={ setSearch } isLoading={ isLoading } /> }
			<Spinner isActive={ isLoading }>
				<div className="eac-table__container">
					<table>
						{ tableCaption }
						{ tableColGroups }
						<thead></thead>
						<tbody></tbody>
					</table>
				</div>
			</Spinner>
			<Pagination
				currentPage={ query?.page }
				perPage={ query?.per_page }
				totalCount={ totalCount }
				onChange={ setPagination }
				isLoading={ isLoading }
				{ ...( typeof pagination === 'object' ? pagination : {} ) }
			/>
		</div>
	);
}

export default forwardRef( Table );
