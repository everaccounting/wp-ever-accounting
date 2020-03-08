import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchTransfers, BulkAction } from 'store/transfers';
import { getHeaders, getBulk } from './constants';
import {Button, Navigation, SearchBox, Table} from '@eaccounting/components';
import Row from './row';
import EditTransfer from "component/edit-transfer";
import {Link} from "react-router-dom";
class Transfers extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding:false
		};
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

				{this.state.isAdding && <EditCategory onClose={this.onClose} onCreate={this.props.onAdd}/>}
				<div className="ea-table-display">
					<Link className="page-title-action" to={`${match.url}/new`}>
						{__('Add Transfer')}
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
	return state.transfers;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchTransfers(params));
		},
		onSetOrderBy: (orderby, order) => {
			dispatch(fetchTransfers({ orderby, order }));
		},
		onChangePage: page => {
			dispatch(fetchTransfers({ page }));
		},
		onSearch: search => {
			dispatch(fetchTransfers({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'TRANSFERS_ALL_SELECTED', payload: onoff });
		},
		onAdd: item => {
			dispatch({ type: 'TRANSFERS_ADDED', item });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Transfers);
