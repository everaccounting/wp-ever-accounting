import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import {Form, Field} from "react-final-form";
export default class EditBill extends Component {
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
				<h1 className="wp-heading-inline">{__('Edit')}</h1>
			</Fragment>
		);
	}
}
