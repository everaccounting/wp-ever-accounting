import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	Button
} from "@eaccounting/components"
import {withTable} from "@eaccounting/hoc";
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import EditTaxRate from "components/edit-taxrate";

class TaxRates extends Component {
	constructor(props) {
		super(props);
		this.state = {
			isAdding: false
		};
	}

	onAdd = ev => {
		ev.preventDefault();
		this.setState({isAdding: !this.state.isAdding});
	};

	onClose = () => {
		this.setState({isAdding: !this.state.isAdding});
	};

	onCreate = () => {
		this.props.invalidateCollection();
		this.setState({isAdding: !this.state.isAdding});
	};

	onRenderRow = (item, pos, isSelected, isLoading, search) => {
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
		const {status, total, items, page, order, orderby, query, selected} = this.props;
		return (
			<Fragment>

				{this.state.isAdding && <EditTaxRate
					onClose={this.onClose}
					onCreate={this.onCreate}
					tittle={__('Add Tax Rate')}
					buttonTittle={__('Add')}/>
				}

				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>{__('Add Tax Rate')}</Button>
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}
					onAction={this.props.onBulkAction}
					bulk={getBulk()}/>

				<Table
					headers={getHeaders()}
					orderby={orderby}
					selected={selected}
					order={order}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetAllSelected={this.props.onAllSelected}
					onSetOrderBy={this.props.onOrderBy}
				/>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}
					onAction={this.props.onAction}
				/>

			</Fragment>
		)
	}
}

export default withTable('taxrates', {orderby: 'created_at'})(TaxRates);
