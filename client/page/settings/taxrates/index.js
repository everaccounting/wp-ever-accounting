import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
export default class TaxRates extends Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<Fragment>
				<h1 className="wp-heading-inline">{__('TaxRates')}</h1>
			</Fragment>
		);
	}
}
