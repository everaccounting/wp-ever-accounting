import {__} from '@wordpress/i18n';
import classnames from 'classnames';
import {useEffect, Fragment} from '@wordpress/element';
import {find, first, isEqual, without} from 'lodash';
import PropTypes from 'prop-types';
import Table from "../table";
import Pagination from "../pagination";
import SearchBox from "../searchbox";
import SubSub from "../subsub";
import BulkActions from "./bulk-actions";
import TableFilter from "./table-filter";

import './style.scss';

function ListTable(props) {
	const {
		className,
		isLoading,
		query,
		columns,
		data,
		total,
		onQueryChange,
		filters,
		title,
		actions
	} = props;

	const {
		page = 1,
		per_page = 20,
		orderby = 'id',
		order = 'desc',
		search = '',
	} = query;

	useEffect(()=> {
		console.log(total);
	}, [total])

	const handleSearch = (search) => {
		dispatchEvent('onQueryChange', {...query, search})
	}

	const handleFilter = (filters) => {
		dispatchEvent('onQueryChange', {...query, ...filters})
	}

	const handleSort = (sort) => {
		dispatchEvent('onQueryChange', {...query, ...sort})
	}

	const handlePagination = (page) => {
		dispatchEvent('onQueryChange', {...query, page})
	}

	const handleActions = (action) => {
		dispatchEvent('onAction', action)
	}

	const dispatchEvent = (name, ...args) => {
		const fn = props[name];
		fn && fn(...args);
	}

	const classes = classnames(
		'ea-list-table-wrapper',
		className
	);

	return (
		<Fragment>
			<div className={classes}>{title && <h1>{title}</h1>}

				<SearchBox isDisabled={!!isLoading} onSearch={handleSearch}/>

				<div className="tablenav top">

					{actions &&
					<BulkActions
						onAction={handleActions}
						actions={actions}/>}

					{filters && <TableFilter
						onFilter={handleFilter}
						filters={filters}/>}

					<Pagination
						onPageChange={handlePagination}
						total={total}
						page={page}
						per_page={per_page}
						isDisabled={!!isLoading}/>
				</div>

				<Table
					columns={columns}
					data={data}
					isLoading={!!isLoading}
					onSortChange={handleSort}/>

				<div className="tablenav bottom">

					{actions && <BulkActions actions={actions}/>}

					<Pagination
						onPageChange={handlePagination}
						total={total}
						page={page}
						per_page={per_page}
						isDisabled={!!isLoading}/>
				</div>
			</div>
		</Fragment>
	)
}

ListTable.propTypes = {
	/**
	 * The title used in the card header, also used as the caption for the content in this table.
	 */
	title: PropTypes.string.isRequired,
	/**
	 * Defines if the table contents are loading.
	 * It will display `Loading` Animation.
	 */
	isLoading: PropTypes.bool,

	/**
	 * Query to push in the table.
	 */
	query: PropTypes.object,

	/**
	 * An array of columns headers (see `Table` props).
	 */
	columns: PropTypes.array,

	/**
	 * An array of columns data (see `Table` props).
	 */
	data: PropTypes.arrayOf(PropTypes.object),

	/**
	 * A function which returns a callback function to update the query string for a given `param`.
	 */
	onQueryChange: PropTypes.func,

	/**
	 * Table Filter.
	 */
	filters: PropTypes.object,

	/**
	 * Render button right of the title
	 */
	buttons: PropTypes.func,

	/**
	 * Table Action.
	 */
	actions: PropTypes.arrayOf(PropTypes.shape({
		key: PropTypes.string,
		label: PropTypes.string
	})),
	/**
	 * Bulk action
	 */
	onAction:PropTypes.func,

}

ListTable.defaultProps = {
	isLoading: false,
	query: {},
	onQueryChange: () => () => {
	},
};

export default ListTable;
