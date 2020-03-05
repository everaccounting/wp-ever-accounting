import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchTransactions, setFilter } from 'store/transactions';
import { getHeaders } from './constants';
import { DateFilter, Navigation, SearchBox, Table, SelectControl } from '@eaccounting/components';
import Row from './row';
import { map } from 'lodash';
import clean from 'lodash-clean';
import AccountControl from 'component/account-control';
import CategoryControl from 'component/category-control';

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {
			filters: {},
		};
	}

	componentDidMount() {
		this.props.onMount({});
	}

	setFilter = (filter, value) => {
		const { filters } = this.props.table;
		const newFilter = clean({ ...filters, [filter]: value ? value : undefined });
		this.props.onFilter(newFilter);
	};

	onFilterAccount = accounts => {
		let filter = map(accounts, 'value') || undefined;
		let account_ids = map(filter, 'id') || undefined;
		this.setFilter('account_id', account_ids);
	};

	onFilterDate = date => {
		this.setFilter('date', date);
	};

	onRenderRow = (item, pos, status, search) => {
		const { selected } = this.props.table;
		return (
			<Row
				item={item}
				key={pos}
				disabled={status.isLoading}
				search={search}
				isSelected={selected.includes(item.id)}
				{...this.props}
			/>
		);
	};

	render() {
		const { status, total, table, rows, match } = this.props;

		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<hr className="wp-header-end" />

				<div className="ea-table-display">
					<SearchBox status={status} table={table} onSearch={this.props.onSearch} />
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					status={status}
				>
					<DateFilter className={'alignleft actions'} date="" onChange={this.onFilterDate} />

					<AccountControl
						placeholder={__('Filter Accounts')}
						className={'alignleft actions'}
						isMulti
						isClearable
						onChange={this.onFilterAccount}
					/>

					<CategoryControl
						placeholder={__('Filter Categories')}
						className={'alignleft actions'}
						isMulti
						isClearable
						onChange={this.onFilterAccount}
					/>

					<SelectControl
						className={'alignleft actions'}
						placeholder={__('Filter Types')}
						options={Object.keys(eAccountingi10n.data.transactionTypes).map(key => {
							return { value: key, label: eAccountingi10n.data.transactionTypes[key] };
						})}
						isMulti
						onChange={this.onFilterType}
					/>
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
					status={status}
				/>
			</Fragment>
		);
	}
}

const mapStateToProps = state => {
	return state.transactions;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchTransactions(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(fetchTransactions({ order_by, order }));
		},
		onChangePage: page => {
			dispatch(fetchTransactions({ page }));
		},
		onSearch: search => {
			dispatch(fetchTransactions({ search }));
		},
		onFilter: filter => {
			dispatch(setFilter(filter));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Transactions);
