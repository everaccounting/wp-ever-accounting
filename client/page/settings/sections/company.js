import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import {
	Form,
	Button,
	TextControl,
	DateControl,
	Icon,
	CompactCard,
	Card,
	Select,
	Spinner,
	Row,
	Col
} from "@eaccounting/components";

export default class Company extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	componentDidCatch(error, info) {
		this.setState({ error: true, stack: error, info });
	}

	render() {
		const {settings} = this.props;
		window.settings = settings;
		console.log(settings);
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('Company Settings')}</CompactCard>
				<Card>
					<Row>
						<Col>
							<TextControl
								label={__('Company Name', 'wp-ever-accounting')}
								required
								value={settings.company_name}
								onChange={(name)=> settings.setCompany_name(name)}
							/>
						</Col>
					</Row>
				</Card>
			</Fragment>
		);
	}
}
