import { Component, Fragment } from 'react';
import {  __ } from '@wordpress/i18n';
import {getPath} from "@eaccounting/navigation"
export default class Defaults extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	render() {
		{console.log(getPath())}
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Defaults')}</h1>
			</Fragment>
		);
	}
}
