import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchReconciliations, BulkAction } from 'store/reconciliations';
import { getHeaders, getBulk } from './constants';
import { Navigation, SearchBox, Table } from '@eaccounting/components';
import Row from './row';
import {Link} from "react-router-dom";

class Reconciliations extends Component {
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

				<div className="ea-table-display">
					<Link className="page-title-action" to={`${match.url}/new`}>
						{__('Add reconciliation')}
					</Link>
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
	return state.reconciliations;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchReconciliations(params));
		},
		onSetOrderBy: (orderby, order) => {
			dispatch(fetchReconciliations({ orderby, order }));
		},
		onChangePage: page => {
			dispatch(fetchReconciliations({ page }));
		},
		onSearch: search => {
			dispatch(fetchReconciliations({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'RECONCILIATIONS_ALL_SELECTED', payload: onoff });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Reconciliations);
