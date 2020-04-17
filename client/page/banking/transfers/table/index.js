import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import {Link} from "react-router-dom";
import {withListTable} from "@eaccounting/hoc";

class Transfers extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		const {match} = this.props;
		return (
			<Fragment>
				<Link className="page-title-action" to={`${match.path}/add`}>{__('Add Transfer')}</Link>
			</Fragment>
		);
	}
}

export default withListTable()(Transfers)
