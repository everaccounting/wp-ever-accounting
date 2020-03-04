import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import {fetchTransactions} from "store/transactions";
import {getHeaders} from "./constants";
import {Navigation, SearchBox, Table} from "@eaccounting/components";
import Row from './row';

class Transactions extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidMount() {
		this.props.onMount({});
	}

	onRenderRow = (item, pos, status, search) => {
		const {selected} = this.props.table;
		return (
			<Row
				item={item}
				key={pos}
				disabled={status.isLoading}
				search={search}
				isSelected={selected.includes(item.id)}
				{...this.props}/>
		);
	};

	goTo = (ev, route) => {
		ev.preventDefault();
		this.props.history.push(route);
	};

	render() {
		const {status, total, table, rows, match} = this.props;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
				<hr className="wp-header-end"/>


				<div className="ea-table-display">

					<SearchBox
						status={status}
						table={table}
						onSearch={this.props.onSearch}/>
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					status={status}/>

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
					status={status}/>


			</Fragment>
		)
	}
}

const mapStateToProps = (state) => {
	return state.transactions
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: (params) => {
			dispatch(fetchTransactions(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(fetchTransactions({order_by, order}));
		},
		onChangePage: (page) => {
			dispatch(fetchTransactions({page}));
		},
		onSearch: (search) => {
			dispatch(fetchTransactions({search}));
		},
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Transactions);
