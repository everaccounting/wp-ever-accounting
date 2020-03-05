/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
import { connect } from 'react-redux';
import { map } from 'lodash';
/**
 * Accounting dependencies
 */
import { Button, Navigation, SearchBox, SelectControl, Table, DateFilter } from '@eaccounting/components';

/**
 * Internal dependencies
 */
import { setGetItems, setPage, setOrderBy, setSearch, setFilter } from 'state/transactions/action';
import { translate as __ } from 'lib/locale';
import { getHeaders } from './constants';
import { STATUS_IN_PROGRESS, STATUS_SAVING, STATUS_COMPLETE } from 'status';
import Row from './row';
import { transactionTypes } from 'state/transactions/initial';
import { getSelectedOptions } from 'lib/table';
import AccountControl from 'component/account-control';
import CategoryControl from 'component/category-control';
// import DateRangeControl from "component/date-range-control";

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false,
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidMount() {
		this.props.onLoadItems();
	}

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

	onRenderRow = (item, pos, status, search) => {
		const { saving } = this.props.transactions;
		const loadingStatus = status.isLoading ? STATUS_IN_PROGRESS : STATUS_COMPLETE;
		const rowStatus = saving.indexOf(item.id) !== -1 ? STATUS_SAVING : loadingStatus;
		return <Row item={item} key={pos} status={rowStatus} search={search} />;
	};

	render() {
		const { status, total, table, rows } = this.props.transactions;
		const { type = [], date = '', category_id, account_id } = table.filterBy;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<Button className="page-title-action" onClick={this.onAdd}>
					{__('Income')}
				</Button>
				<Button className="page-title-action" onClick={this.onAdd}>
					{__('Payments')}
				</Button>
				<hr className="wp-header-end" />

				<div className="ea-table-display">
					<SearchBox status={status} table={table} onSearch={this.props.onSearch} />
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
				>
					<DateFilter className={'alignleft actions'} date={date} onChange={this.onFilterDate} />

					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Filter Type')}
						options={transactionTypes}
						isMulti
						isDisabled={status !== STATUS_COMPLETE}
						value={getSelectedOptions(transactionTypes, type)}
						onChange={this.onFilterType}
					/>

					<AccountControl
						className={'alignleft actions'}
						isMulti
						isClearable
						isDisabled={status !== STATUS_COMPLETE}
						selected={account_id}
						onChange={this.onFilterAccount}
					/>

					<CategoryControl
						className={'alignleft actions'}
						isMulti
						isClearable
						isDisabled={status !== STATUS_COMPLETE}
						selected={category_id}
						onChange={this.onFilterCategory}
					/>
				</Navigation>

				<Table
					headers={getHeaders()}
					rows={rows}
					total={total}
					row={this.onRenderRow}
					table={table}
					status={status}
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
		onLoadItems: () => {
			dispatch(setGetItems());
		},
		onChangePage: page => {
			dispatch(setPage(page));
		},
		onAction: action => {
			dispatch(setBulkAction(action));
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
