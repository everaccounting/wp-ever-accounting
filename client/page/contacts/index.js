import {Component, Fragment} from 'react';
import withContacts from "hocs/with-contacts";
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

class Contacts extends Component {
	constructor(props) {
		super(props);
		this.state = {};
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
		const {items, headers} = this.props;
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Contacts')}</h1>
				<hr className="wp-header-end"/>
				<Table
					headers={getHeaders()}
					orderby={'name'}
					selected={[]}
					order={'desc'}
					rows={items}
					total={100}
					row={this.onRenderRow}
					status={"STATUS_COMPLETE"}
					onSetAllSelected={this.props.onSetAllSelected}
					onSetOrderBy={this.props.onSetOrderBy}
				/>

			</Fragment>
		);
	}
}

export default compose(withSelect((select) => {
	const {getCollection, getCollectionHeaders, getCollectionHeader,hasStartedResolution, isResolving} = select(COLLECTIONS_STORE_KEY);
	console.log(getCollectionHeader('x-wp-total', '/ea/v1', 'contacts'));
	//console.log(isResolving);
	console.log(isResolving('getCollection'));
	console.log(hasStartedResolution('getCollection'));
	return {
		items: getCollection('/ea/v1', 'contacts'),
		headers: getCollectionHeaders('/ea/v1', 'contacts'),
	}
}), withDispatch((dispatch) => {

}))(Contacts);
