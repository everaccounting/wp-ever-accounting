import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
export default class Settings extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Settings')}</h1>
			</Fragment>
		);
	}
}
