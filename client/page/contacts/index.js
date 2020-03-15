import {Component, Fragment} from 'react';
import withContacts from "./with-contact";
import {
	SearchBox,
	TableNav,
	Table,
	SelectControl,
	AccountControl,
	CategoryControl,
	DateFilter
} from "@eaccounting/components"
import {getHeaders, getBulk} from './constants';
import Row from "./row";
import {getOptions} from "options";
import {__} from '@wordpress/i18n';
import {map} from "lodash"
import {COLLECTIONS_STORE_KEY, QUERY_STATE_STORE_KEY} from "data";
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
//
// class Contacts extends Component {
// 	constructor(props) {
// 		super(props);
// 		this.state = {};
// 	}
//
//
// 	onRenderRow = (item, pos, isSelected, isLoading, search) => {
// 		return (
// 			<Row
// 				item={item}
// 				key={pos}
// 				isLoading={isLoading}
// 				search={search}
// 				isSelected={isSelected}
// 				{...this.props}
// 			/>
// 		)
// 	};
//
// 	render() {
// 		const {items, total} = this.props;
// 		const {page = 1} = this.props.query;
// 		const selected = [];
// 		return (
// 			<Fragment>
// 				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
// 				<hr className="wp-header-end"/>
// 				<div className="ea-table-display">
// 					<SearchBox status={status} onSearch={search => { this.setQuery('search', search)}}/>
// 				</div>
//

//
// 				<Table
// 					headers={getHeaders()}
// 					orderby={'name'}
// 					selected={[]}
// 					order={'desc'}
// 					rows={items}
// 					total={total}
// 					row={this.onRenderRow}
// 					status={"STATUS_COMPLETE"}
// 					onSetAllSelected={this.props.onSetAllSelected}
// 					onSetOrderBy={this.props.onSetOrderBy}
// 				/>
//
// 			</Fragment>
// 		);
// 	}
// }

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
					onAction={this.props.onAction}
					bulk={getBulk()}
				/>
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
