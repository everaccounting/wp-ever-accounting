import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
export default class Accounts extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Accounts')}</h1>
			</Fragment>
		);
	}
}
