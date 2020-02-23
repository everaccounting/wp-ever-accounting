/**
 * External dependencies
 */

import {Component, Fragment} from 'react';
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';

/**
 * Internal dependencies
 */
import './style.scss';
import Table from 'component/table';
import Filter from 'component/table/filter';
import TableNav from 'component/table/navigation';
import SearchBox from 'component/search-box';
import DateFilter from 'component/date-filter';
import TransactionsRow from './row';
import Placeholder from "../../component/placeholder";
import {
	getTransactions,
	createTransaction,
	setPage,
	performTableAction,
	setAllSelected,
	setOrderBy,
	setSearch,
	setFilter,
	setDisplay
} from 'state/transactions/action';
import {isEnabled} from 'component/table/utils';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS, STATUS_SAVING} from 'lib/status';
import {getHeaders} from './constants';
import EditTransaction from 'component/edit-account';
import {initialTransaction} from 'state/accounts/selection';
import AccountSelection from 'component/account-selection';

class Transactions extends Component {
	constructor(props) {
		super(props);
		// this.state = {
		// 	isAdding:false
		// };
		// window.addEventListener('popstate', this.onPageChanged);
	}

	// componentDidCatch(error, info) {
	// 	this.setState({error: true, stack: error, info});
	// }
	//
	// componentWillUnmount() {
	// 	window.removeEventListener('popstate', this.onPageChanged);
	// }

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


	// setFilter = (filterName, filterValue) => {
	// 	const {filterBy} = this.props.accounts.table;
	//
	// 	this.props.onFilter({...filterBy, [filterName]: filterValue ? filterValue : undefined});
	// };

	// getHeaders( selected ) {
	// 	return getHeaders().filter( header => isEnabled( selected, header.name ) || header.name === 'cb' || header.name === 'name' );
	// }
	//
	// onAdd = ev =>{
	// 	ev.preventDefault();
	// 	this.setState({isAdding:!this.state.isAdding});
	// };
	//
	// onClose = () =>{
	// 	this.setState({isAdding:!this.state.isAdding});
	// };

	render() {
		const {status, total, table, rows} = this.props.transactions;
		return (
			<Fragment>
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
					<Filter>
						{__('Filter')}
					</Filter>
					<Filter>
						<DateFilter/>
					</Filter>
					<Filter>
						<AccountSelection/>
					</Filter>
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
		onSetAllSelected: onoff => {
			dispatch(setAllSelected(onoff));
		},
		onSetOrderBy: (column, order) => {
			dispatch(setOrderBy(column, order));
		},
		onFilter: (filterBy) => {
			dispatch(setFilter(filterBy));
		},
		onSearch: (search) => {
			dispatch(setSearch(search));
		},
		onCreate: item => {
			dispatch(createTransaction(item));
		},
		onSetDisplay: (displayType, displaySelected) => {
			dispatch(setDisplay(displayType, displaySelected));
		},
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps,
)(Transactions);
