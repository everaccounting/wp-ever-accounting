import {Component, Fragment} from 'react';
import withAccounts from "hocs/with-accounts";
import {SearchBox, TableNav, Table} from "@eaccounting/components"
import {getHeaders, getBulk} from './constants';
import Row from "./row";

class Accounts extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	onRenderRow = (item, pos, isSelected, isLoading, search) => {
		return(
			<Row
				item={item}
				key={pos}
				isLoading={isLoading}
				search={search}
				isSelected={isSelected}
				{...this.props}
			/>
		)
	};

	render() {
		const {status, total, items, query, selected} = this.props;
		const {page = 1, orderby = 'name', order = 'desc'} = query;

		return (
			<Fragment>
				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
					bulk={getBulk()}
				/>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					selected={selected}
					order={order}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={this.props.onSetOrderBy}
				/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onChangePage}
					onAction={this.props.onAction}
				/>

			</Fragment>
		);
	}
}

export default withAccounts(Accounts);
