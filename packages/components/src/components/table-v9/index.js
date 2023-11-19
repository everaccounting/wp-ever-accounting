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
import TableHeader from './header';
import Body from './body';
import { useQuery, useColumns, useSelection, useExpandable } from './hooks';
import Empty from '../empty';

function Table( props, ref ) {
	const {
		query = {},
		columns: rawColumns,
		data,
		totalCount,
		status,
		caption,
		search = true,
		actions,
		toolbar = true,
		onChange,
		rowKey,
		rowStyle,
		renderExpanded,
		showSummary,
		renderSummary,
		pagination = true,
		emptyMessage,
		errorMessage,
		selectedItems: rawSelectedItems,
		style,
		className,
		bordered,
	} = props;
	const { columns } = useColumns( rawColumns );
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

	const classes = classNames( 'eac-table', className, {
		'eac-table--empty': ! hasData && ! isLoading,
		'eac-table--bordered': !! bordered,
		'eac-table--loading': !! isLoading,
	} );

	return (
		<div className={ classes } style={ style } ref={ ref }>
			<div className="eac-table__container">
				<table>
					<thead>
						<TableHeader
							isLoading={ isLoading }
							columns={ columns }
							orderby={ query?.orderby }
							order={ query?.order }
							onSort={ setSort }
							// isAllSelected={ isAllSelected }
							// onSelectAll={ onSelectAll }
						/>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	);
}

export default forwardRef( Table );
