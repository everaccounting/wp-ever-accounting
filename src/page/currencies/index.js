import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {connect} from "react-redux";
import {fetchCurrencies, BulkAction} from "store/currencies";
import {getHeaders, getBulk} from "./constants";
import {Navigation, SearchBox, Table} from "@eaccounting/components";
import Row from './row';

class Currencies extends Component {
	constructor( props ) {
		super(props);
		this.state = {};
	}

	componentDidCatch( error, info ) {
		this.setState( { error: true, stack: error, info } );
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


	render() {
		const {status, total, table, rows, match} = this.props;
		return(
			<Fragment>
				<a className="page-title-action">{__('Add Category')}</a>

				<div className="ea-table-display">
					<SearchBox
						status={status}
						table={table}
						onSearch={this.props.onSearch}
					/>
				</div>

				<Navigation
					total={total}
					selected={table.selected}
					table={table}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					status={status}
					bulk={getBulk()}/>

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
					status={status}/>

			</Fragment>
		)
	}
}


const mapStateToProps = (state) => {
	return state.currencies
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: (params) => {
			dispatch(fetchCurrencies(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(fetchCurrencies({order_by, order}));
		},
		onChangePage: (page) => {
			dispatch(fetchCurrencies({page}));
		},
		onSearch: (search) => {
			dispatch(fetchCurrencies({search}));
		},
		onSetAllSelected: (onoff) => {
			dispatch({type: "CURRENCIES_ALL_SELECTED", payload: onoff});
		},
		onAction: (action) => {
			dispatch(BulkAction(action));
		}
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Currencies);
