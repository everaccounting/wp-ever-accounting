import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withListTable} from "@eaccounting/hoc";
import {SearchBox} from "@eaccounting/components"

class Items extends Component {
	constructor(props) {
		super(props);
	}

	// componentDidMount() {
	// 	this.loadItems();
	// }
	//
	// loadItems(params = null ){
	// 	this.props.setTable('accounts', params)
	// }

	render() {
		// const {status} = this.props.table;
		console.log(this.props);
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Items')}</h1>

				<div className="ea-table-display">
					{/*<SearchBox status={status} onSearch={this.props.onSearch}/>*/}
				</div>

			</Fragment>
		);
	}
}

export default withListTable({
	queryFilter: (query) => {
		return {per_page:10}
	}
})(Items);
