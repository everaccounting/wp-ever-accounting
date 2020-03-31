import {Component, Fragment} from 'react';
import {
	SearchBox,
	TableNav,
	Table,
	ContactTypesControl,
	Button
} from "@eaccounting/components"
import {withTable} from "@eaccounting/hoc";
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {__} from '@wordpress/i18n';
import EditContact from "components/edit-contact";

class Contacts extends Component {
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
				{this.state.isAdding && <EditContact onClose={this.onClose} onCreate={this.onCreate} tittle={__('Add Contact')} buttonTittle={__('Submit')}/>}
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<Button className="page-title-action" onClick={this.onAdd}>{__('Add Contact')}</Button>
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
						isClearable
						onChange={(type) => this.props.onFilter({type})}/>
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

export default withTable('contacts', {orderby: 'created_at'})(Contacts);
