import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import {Form, TextControl, TextareaControl, DateControl, Icon, CompactCard, Card, Select} from "@eaccounting/components";
import {getPath} from "@eaccounting/navigation"

export default class Company extends Component {
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
				<CompactCard tagName="h3">{__('Company Settings')}</CompactCard>
				<Card>

				</Card>
			</Fragment>
		);
	}
}
