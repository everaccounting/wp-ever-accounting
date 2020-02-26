/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import {map} from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import Table from 'component/table';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import DateFilter from 'component/date-filter';
import TransactionsRow from './row';
import {
	getTransactions,
	setPage,
	performTableAction,
	setOrderBy,
	setSearch,
	setFilter,
} from 'state/transactions/action';
import {getHeaders} from './constants';
import AccountControl from 'component/account-control';
import CategoryControl from 'component/category-control';
import moment from 'moment';
import { TextControl, ReactSelect} from "@eaccounting/components";
import {Button} from "@wordpress/components";


const typeFilter = [
	{
		label: __('Income'),
		value: 'income',
	},
	{
		label: __('Expense'),
		value: 'expense',
	}
];

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false
		};
		window.addEventListener('popstate', this.onPageChanged);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	componentDidMount() {
		this.props.onLoadTransactions();
	}

	onRenderRow = (item, pos, status, search) => {
		return (
			<TransactionsRow
				item={item}
				key={pos}
			/>
		);
	};

	setFilter = (filter, value) => {
		const { filterBy } = this.props.transactions.table;
		this.props.onFilter( { ...filterBy, [ filter ]: value ? value : undefined } );
	};

	onFilterAccount = (accounts) => {
		this.setFilter('account_id', map(accounts, 'value'));
	};

	onFilterCategory = (categories) => {
		this.setFilter('category_id', map(categories, 'value'));
	};

	onFilterDate = (start, end) => {
		let start_date, end_date;
		start_date = start.format('YYYY-MM-DD');
		end_date = end.format('YYYY-MM-DD');
		this.setFilter('date', `${start_date}_${end_date}` );
	};

	onFilterType = (types) => {
		this.setFilter('type', map(types, 'value'));
	};

	onResetFilter = () => {
		this.props.onLoadTransactions({filter:{}});
	};

	render() {
		const {status, total, table, rows} = this.props.transactions;
		const {type = [], date = ''} = table.filterBy;
		//
		// const isFilterApplied = Object.keys(table.filter).length > 0;
		//
		const types = typeFilter.filter((filter, index) => {
			return type.includes(filter.value) === true;
		});

		let dates = date.split('_', 2);

		let startDate = dates['0'] !== undefined ? moment(dates['0']) : undefined;
		let endDate = dates['1'] !== undefined ? moment(dates['1']) : undefined;
		let date_range = '';
		if (date && startDate && endDate) {
			date_range = startDate.format('D MMM Y');
			date_range += ' - ' + endDate.format('D MMM Y');
		}

		return (
			<Fragment>
				<pre>
						{JSON.stringify(table)}
				</pre>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<hr className="wp-header-end"/>
				<div className="ea-table-display">
					<SearchBox
						status={status}
						onSearch={this.props.onSearch}
					/>
				</div>

				<TableNav
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}>

					<DateFilter
						className={'alignleft actions'}
						startDate={startDate}
						endDate={endDate}
						onChange={this.onFilterDate}>

						<TextControl
							autoComplete='off'
							placeholder={__('Select Date')}
							value={date_range}
							onChange={() => {
							}}/>
					</DateFilter>

					<AccountControl
						className={'alignleft actions'}
						isMulti
						isClearable
						selected={table.filterBy.account_id?table.filterBy.account_id:[]}
						onChange={this.onFilterAccount}/>

					<CategoryControl
						className={'alignleft actions'}
						isMulti
						isClearable
						selected={table.filterBy.category_id?table.filterBy.category_id:[]}
						onChange={this.onFilterCategory}/>


					<ReactSelect
						className={'alignleft actions'}
						placeholder={__('Select Type')}
						options={typeFilter}
						isMulti
						value={types}
						onChange={this.onFilterType}/>
					{/*{isFilterApplied && <Button*/}
					{/*	onClick={this.onResetFilter}*/}
					{/*	className={'alignleft actions'}*/}
					{/*	isDefault>{__('Reset')}</Button>}*/}
				</TableNav>

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

				<TableNav
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}/>


			</Fragment>
		);
	}
}

function mapStateToProps(state) {
	const {transactions} = state;
	return {
		transactions,
	};
}

function mapDispatchToProps(dispatch) {
	return {
		onLoadTransactions: () => {
			dispatch(getTransactions());
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
		onFilter: (filter) => {
			dispatch(setFilter(filter));
		},
		onSearch: (search) => {
			dispatch(setSearch(search));
		}
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(Transactions);
