import {Component, Fragment} from "react";
import {translate as __} from 'lib/locale';
import {connect} from 'react-redux';
import {fetchRevenues, BulkAction} from "store/revenues";
import {getHeaders, getBulk} from "./constants";
import {Navigation, SearchBox, Table} from "@eaccounting/components";
import {Link} from 'react-router-dom';
import Row from './row';

class Revenues extends Component {
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


	render() {
		const {status, total, table, rows, match} = this.props;
		return (
			<Fragment>
				<div className="ea-table-display">
					<Link className="page-title-action" to={`${match.url}/new`}>{__('Add Revenue')}</Link>
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
	return state.revenues
};

function mapDispatchToProps(dispatch) {
	return {
		onMount: (params) => {
			dispatch(fetchRevenues(params));
		},
		onSetOrderBy: (order_by, order) => {
			dispatch(fetchRevenues({order_by, order}));
		},
		onChangePage: (page) => {
			dispatch(fetchRevenues({page}));
		},
		onSearch: (search) => {
			dispatch(fetchRevenues({search}));
		},
		onSetAllSelected: (onoff) => {
			dispatch({type: "REVENUES_ALL_SELECTED", payload: onoff});
		},
		onAction: (action) => {
			dispatch(BulkAction(action));
		}
	}
}

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Revenues);
