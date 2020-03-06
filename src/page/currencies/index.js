import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchCurrencies, BulkAction } from 'store/currencies';
import { getHeaders, getBulk } from './constants';
import {Button, Navigation, SearchBox, Table} from '@eaccounting/components';
import Row from './row';
import EditCurrency from "component/edit-currency";
class Currencies extends Component {
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

	onAdd = ev => {
		ev.preventDefault();
		this.setState({ isAdding: !this.state.isAdding });
	};

	onClose = () => {
		this.setState({ isAdding: !this.state.isAdding });
	};

	render() {
		const { status, total, table, rows, match } = this.props;
		return (
			<Fragment>
				<a className="page-title-action" onClick={this.onAdd}>{__('Add Currency')}</a>
				{this.state.isAdding && <EditCurrency onClose={this.onClose} onCreate={this.props.onAdd}/>}

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
	return state.currencies;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchCurrencies(params));
		},
		onSetOrderBy: (orderby, order) => {
			dispatch(fetchCurrencies({ orderby, order }));
		},
		onChangePage: page => {
			dispatch(fetchCurrencies({ page }));
		},
		onSearch: search => {
			dispatch(fetchCurrencies({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'CURRENCIES_ALL_SELECTED', payload: onoff });
		},
		onAdd: item => {
			dispatch({ type: 'CURRENCIES_ADDED', item });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(Currencies);
