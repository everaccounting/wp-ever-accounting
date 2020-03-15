import {Component, Fragment} from 'react';
import withContacts from "./with-contact";
import {
	SearchBox,
	TableNav,
	Table,
	SelectControl,
	AccountControl,
	CategoryControl,
	DateFilter,
	ContactTypesControl
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
		const {status, items, page, total, selected = []} = this.props;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<hr className="wp-header-end"/>
				<div className="ea-table-display">
					<SearchBox status={status} onSearch={search => {
						this.props.setQuery('search', search)
					}}/>
				</div>
				<TableNav
					status={status}
					total={total}
					page={page}
					selected={selected}
					onChangePage={(page) => {
						this.props.setQuery('page', page)
					}}
					onAction={this.props.onBulkAction}
					bulk={getBulk()}
				>
					<ContactTypesControl
						className={'alignleft actions'}/>
						
				</TableNav>
				<Table
					headers={getHeaders()}
					orderby={'name'}
					selected={selected}
					order={'desc'}
					rows={items}
					total={total}
					row={this.onRenderRow}
					status={status}
					onSetAllSelected={this.props.onAllSelected}
					onSetOrderBy={(orderby, order) => this.props.setQuery(orderby, order)}
				/>

			</Fragment>
		)
	}
}

export default withContacts('contacts')(Contacts);
