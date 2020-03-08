/**
 * External dependencies
 */

import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { map } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import { SelectControl, Table, Navigation, SearchBox, BulkAction, Button } from '@eaccounting/components';
import DateRangeControl from 'component/date-range-control';
import TransactionsRow from './row';
import { setGetItems, setPage, setBulkAction, setOrderBy, setSearch, setFilter } from 'state/transactions/action';
import { getHeaders } from './constants';
import AccountControl from 'component/account-control';
import CategoryControl from 'component/category-control';
import moment from 'moment';

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	componentDidMount() {
		this.props.onLoadTransactions();
	}

	onRenderRow = (item, pos, status, search) => {
		return <TransactionsRow item={item} key={pos} />;
	};

	setFilter = (filter, value) => {
		const { filterBy } = this.props.transactions.table;
		this.props.onFilter({ ...filterBy, [filter]: value ? value : undefined });
	};

	onFilterAccount = accounts => {
		let filter = map(accounts, 'value') || undefined;
		this.setFilter('account_id', filter);
	};

	onFilterCategory = categories => {
		this.setFilter('category_id', map(categories, 'value'));
	};

	onFilterDate = date => {
		this.setFilter('date', date);
	};

	onFilterType = types => {
		this.setFilter('type', map(types, 'value'));
	};

	onResetFilter = () => {
		this.props.onFilter({});
	};

	render() {
		const { status, total, table, rows } = this.props.transactions;
		const { type = [], date = '', category_id, account_id } = table.filterBy;
		const isFiltered = Object.keys(table.filterBy).length > 0;
		const types = typeFilter.filter((filter, index) => {
			return type.includes(filter.value) === true;
		});

		return (
			<Fragment>
				<pre>{JSON.stringify(table)}</pre>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<hr className="wp-header-end" />

				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch} />
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
				>
					{/*<DateRangeControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	date={date}*/}
					{/*	onChange={this.onFilterDate}/>*/}

					{/*<AccountControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	isMulti*/}
					{/*	isClearable*/}
					{/*	selected={account_id}*/}
					{/*	onChange={this.onFilterAccount}/>*/}

					{/*<CategoryControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	isMulti*/}
					{/*	isClearable*/}
					{/*	selected={category_id}*/}
					{/*	onChange={this.onFilterCategory}/>*/}
					{/*<SelectControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	placeholder={__('Select Type')}*/}
					{/*	options={typeFilter}*/}
					{/*	isMulti*/}
					{/*	value={types}*/}
					{/*	onChange={this.onFilterType}/>*/}
				</Navigation>

				<Table
					headers={getHeaders()}
					rows={rows}
					total={total}
					row={this.onRenderRow}
					table={table}
					status={status}
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={this.props.onSetOrderBy}
				/>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
				/>
			</Fragment>
		);
	}
}

function mapStateToProps(state) {
	const { transactions } = state;
	return {
		transactions,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		onLoadTransactions: () => {
			dispatch(setGetItems());
		},
		onChangePage: page => {
			dispatch(setPage(page));
		},
		onAction: action => {
			dispatch(performTableAction(action));
		},
		onSetOrderBy: (column, order) => {
			dispatch(setOrderBy(column, order));
		},
		onFilter: filter => {
			dispatch(setFilter(filter));
		},
		onSearch: search => {
			dispatch(setSearch(search));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Transactions);
