import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
export default class Transactions extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('Transactions')}</h1>
			</Fragment>
		);
	}
}
