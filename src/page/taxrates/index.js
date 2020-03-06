import { Component, Fragment } from 'react';
import { translate as __ } from 'lib/locale';
import { connect } from 'react-redux';
import { fetchTaxRates, BulkAction } from 'store/taxrates';
import { getHeaders, getBulk } from './constants';
import { Navigation, SearchBox, Table } from '@eaccounting/components';
import Row from './row';

class TaxRates extends Component {
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
				<a className="page-title-action">{__('Add Category')}</a>

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
	return state.taxrates;
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: params => {
			dispatch(fetchTaxRates(params));
		},
		onSetOrderBy: (orderby, order) => {
			dispatch(fetchTaxRates({ orderby, order }));
		},
		onChangePage: page => {
			dispatch(fetchTaxRates({ page }));
		},
		onSearch: search => {
			dispatch(fetchTaxRates({ search }));
		},
		onSetAllSelected: onoff => {
			dispatch({ type: 'TAXRATES_ALL_SELECTED', payload: onoff });
		},
		onAction: action => {
			dispatch(BulkAction(action));
		},
	};
}

export default connect(mapStateToProps, mapDispatchToProps)(TaxRates);
