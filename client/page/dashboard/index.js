import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {AccountSelect} from "@eaccounting/components";
export default class Dashboard extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Dashboard')}</h1>
				<AccountSelect/>
			</Fragment>
		);
	}
}

