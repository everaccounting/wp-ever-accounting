import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Form,
	Button,
	TextControl,
	DateControl,
	Icon,
	CompactCard,
	Card,
	SelectControl,
	Spinner, Field
} from "@eaccounting/components";

export default class Defaults extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	form() {
		return (
			<Form onSubmit={this.props.onSubmit} initialValues={this.props.settings}>
				<div className="ea-double-columns">
					<Field
						label={__('Financial Year Start', 'wp-ever-accounting')}
						name="financial_start"
						required>
						{props => (
							<DateControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Date Format', 'wp-ever-accounting')}
						name="date_format"
						required>
						{props => (
							<SelectControl {...props.input} {...props}/>
						)}
					</Field>
				</div>
				<Button isPrimary type="Submit">{__('Submit', 'wp-ever-accounting')}</Button>
			</Form>
		)
	}

	render() {
		const {isLoading} = this.props;
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('General Settings')}</CompactCard>
				<Card> {isLoading ? <Spinner/> : this.form()}</Card>
			</Fragment>
		)
	}
}
