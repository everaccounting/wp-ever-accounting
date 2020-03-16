import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	ContactTypesControl,
	withTable
} from "@eaccounting/components"
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
class Contacts extends Component {
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
		const {status, total, items, query, selected} = this.props;
		const {page = 1, orderby = 'created_at', order = 'desc'} = query;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<a className="page-title-action">{__('Add Contact')}</a>
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

					<ContactTypesControl
						className={'alignleft actions'}
						onChange={(type) => this.props.onFilter({type: type.value})}/>

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
					onAction={this.props.onAction}
				/>

			</Fragment>
		)
	}
}
export default withTable('contacts', {orderby:'created_at'})(Contacts);
