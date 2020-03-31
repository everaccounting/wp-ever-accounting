import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {
	Form,
	Button,
	TextControl,
	Field,
	CompactCard,
	Card,
	Spinner,
	CountryControl
} from "@eaccounting/components";

export default class Company extends Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	form() {
		return (
			<Form onSubmit={this.props.onSubmit} initialValues={this.props.settings}>
				<div className="ea-double-columns">
					<Field
						label={__('Company Name', 'wp-ever-accounting')}
						name="company_name"
						required>
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Company Email', 'wp-ever-accounting')}
						name="company_email"
						required>
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Phone Number', 'wp-ever-accounting')}
						name="company_phone">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Tax Number', 'wp-ever-accounting')}
						name="company_tax_number">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Address', 'wp-ever-accounting')}
						name="company_address">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('City', 'wp-ever-accounting')}
						name="company_city">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('State', 'wp-ever-accounting')}
						name="company_state">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Postcode', 'wp-ever-accounting')}
						name="company_postcode">
						{props => (
							<TextControl {...props.input} {...props}/>
						)}
					</Field>
					<Field
						label={__('Country', 'wp-ever-accounting')}
						name="company_country"
						required>
						{props => (
							<CountryControl {...props.input} {...props}/>
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
				<CompactCard tagName="h3">{__('Company Settings')}</CompactCard>
				<Card> {isLoading ? <Spinner/> : this.form()}</Card>
			</Fragment>
		)
	}
}
