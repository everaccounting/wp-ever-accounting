import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
export default class EditAccount extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		console.log(this.props);
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('EditAccount')}</h1>
			</Fragment>
		);
	}
}
