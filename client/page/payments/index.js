import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	// DateFilter, AccountControl, CategoryControl
} from "@eaccounting/components"
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import {map} from "lodash"
import {Link} from "react-router-dom"
import {withTable} from "@eaccounting/hoc"

class Payments extends Component {
	constructor(props) {
		super(props);
	}

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
		const {match} = this.props;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Payments')}</h1>
				<Link className="page-title-action" to={`${match.path}/add`}>{__('Add payment')}</Link>
				<hr className="wp-header-end"/>
				<div className="ea-table-display">
					<SearchBox status={status} onSearch={this.props.onSearch}/>
				</div>

				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={this.props.onPageChange}
					onAction={this.props.onAction}
					bulk={getBulk()}>

					{/*<DateFilter*/}
					{/*	className={'alignleft actions'}*/}
					{/*	onChange={date => this.props.onFilter({date})}/>*/}

					{/*<AccountControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	placeholder={__('Filter Account')}*/}
					{/*	isMulti*/}
					{/*	onChange={(accounts) => this.props.onFilter({account_id: map(accounts, 'id')})}*/}
					{/*/>*/}

					{/*<CategoryControl*/}
					{/*	className={'alignleft actions'}*/}
					{/*	placeholder={__('Filter Category')}*/}
					{/*	isMulti*/}
					{/*	type={['income', 'expense']}*/}
					{/*	onChange={(categories) => this.props.onFilter({category_id: map(categories, 'id')})}*/}
					{/*/>*/}

				</TableNav>

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
				/>

			</Fragment>
		)
	}
}
export default withTable('payments', {orderby:'created_at'})(Payments);

