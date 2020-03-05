import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchPayments, BulkAction } from 'store/payments';
import { getHeaders, getBulk } from './constants';
import { Navigation, SearchBox, Table } from '@eaccounting/components';
import Row from './row';

class Payments extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	componentDidMount() {
		this.props.onMount({});
	}

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
				<h1 className="wp-heading-inline">{__('Payments')}</h1>

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
					bulk={getBulk()}
				/>

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

const mapStateToProps = state => {
	return state.payments;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchPayments(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(fetchPayments({ order_by, order }));
		},
		onChangePage: page => {
			dispatch(fetchPayments({ page }));
		},
		onSearch: search => {
			dispatch(fetchPayments({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'PAYMENTS_ALL_SELECTED', payload: onoff });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Payments);
