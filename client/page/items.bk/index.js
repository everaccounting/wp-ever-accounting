import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	Button
} from "@eaccounting/components"
import {withTable} from "@eaccounting/hoc";
import {getHeaders} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';

class Items extends Component {
	constructor(props) {
		super(props);
		this.onRenderRow = this.onRenderRow.bind(this);
	}

	onRenderRow(item, pos, isSelected, isLoading, search) {
		return (
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
		const {status, total, items, page, order, orderby, query, selected, history} = this.props;
		console.log(this.props);
		return (
			<Fragment>

				<h1 className="wp-heading-inline">{__('Items')}</h1>
				<button className="page-title-action" onClick={()=> history.push(`${history.location.pathname}/add`)}>{__('Add Item')}</button>
				<hr className="wp-header-end"/>

				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					onChangePage={this.props.onPageChange}/>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					order={order}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetOrderBy={this.props.onOrderBy}/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}/>

			</Fragment>
		);
	}
}

export default withTable('items', {orderby:'created_at'})(Items)
