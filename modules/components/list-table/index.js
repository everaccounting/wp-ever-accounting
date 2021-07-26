/**
 * External dependencies
 */
import { omit, isEmpty } from 'lodash';
import PropTypes from 'prop-types';
import classnames from 'classnames';
import { getActiveFiltersFromQuery } from '@eaccounting/navigation';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Table from '../table';
import TableNav from './table-nav';
import TableSearch from './table-search';
import BulkActions from './bulk-actions';
import AdvancedFilters from '../advaced-filters';
import './style.scss';
import { Button, Icon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function ListTable( props ) {
	const {
		className,
		columns,
		rows,
		total,
		isRequesting,
		query,
		bulkActions,
		filters,
	} = props;

	const { paged = 1, per_page = 20 } = query;
	const [ selected, setSelected ] = useState( [] );
	const [ isFilterActive, setFilterActive ] = useState( false );
	const activeFilters = getActiveFiltersFromQuery( filters, query );
	const classes = classnames( 'ea-list-table-wrapper', className );

	const handleSearch = ( search ) => {
		dispatchEvent( 'onSearch', search );
		dispatchEvent( 'onQueryChange', { ...query, search, paged: 1 } );
	};

	const handleSort = ( sort ) => {
		dispatchEvent( 'onSort', sort );
		dispatchEvent( 'onQueryChange', { ...query, ...sort, paged: 1 } );
	};

	const handlePagination = ( paged ) => {
		dispatchEvent( 'onPageChange', paged );
		dispatchEvent( 'onQueryChange', { ...query, paged } );
	};

	const handleBulkAction = ( action, selected ) => {
		dispatchEvent( 'onBulkAction', action, selected );
		setSelected( [] );
	};

	const handleFilter = ( newFilters ) => {
		dispatchEvent( 'onQueryChange', {
			...omit( query, Object.keys( filters ) ),
			...newFilters,
			paged: 1,
		} );
	};

	const dispatchEvent = ( name, ...args ) => {
		const fn = props[ name ];
		if ( fn ) {
			fn( ...args );
		}
	};

	return (
		<div className={ classes }>
			{ filters && ( ! isEmpty( activeFilters ) || isFilterActive ) && (
				<AdvancedFilters
					onUpdateFilter={ handleFilter }
					filters={ filters }
					query={ query }
					isDisabled={ isRequesting }
				/>
			) }
			<TableSearch
				isDisabled={ isRequesting }
				onSearch={ handleSearch }
			/>
			<div className="tablenav top">
				{ bulkActions && (
					<BulkActions
						selectedItems={ selected }
						actions={ bulkActions }
						onAction={ handleBulkAction }
					/>
				) }

				{ ! isEmpty( Object.keys( filters ) ) && (
					<Button
						isSecondary
						onClick={ () => setFilterActive( ! isFilterActive ) }
					>
						<Icon icon="filter" />
						{ __( 'Filter' ) }
					</Button>
				) }

				<TableNav
					onPageChange={ handlePagination }
					total={ total }
					paged={ paged }
					per_page={ per_page }
				/>
			</div>
			<Table
				query={ query }
				rows={ rows }
				isRequesting={ isRequesting }
				selected={ selected }
				onChangeSelected={ setSelected }
				columns={ columns }
				onSort={ handleSort }
			/>
			<div className="tablenav top">
				<TableNav
					onPageChange={ handlePagination }
					total={ total }
					paged={ paged }
					per_page={ per_page }
				/>
			</div>
		</div>
	);
}

ListTable.propTypes = {
	// An array of columns, as objects.
	columns: PropTypes.array.isRequired,

	// An array of filters, as objects.
	filters: PropTypes.object.isRequired,

	// Bulk actions.
	bulkActions: PropTypes.array,

	// On bulk actions.
	onBulkAction: PropTypes.func,

	// On query change.
	onQueryChange: PropTypes.func,
};

ListTable.defaultProps = {
	filters: {},
};

export default ListTable;
