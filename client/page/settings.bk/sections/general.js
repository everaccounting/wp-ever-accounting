import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withSelect, withDispatch} from "@wordpress/data";
import {withPreloader} from "@eaccounting/hoc/";
import {compose} from '@wordpress/compose';
import {Form, Field} from "react-final-form";
import {
	TextControl,
	FileControl,
	Card,
	Button,
	CountryControl
} from "@eaccounting/components";
import apiFetch from "@wordpress/api-fetch";

class General extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(data){
		data.logo_id = data.logo && data.logo.id && data.logo.id || null;
		apiFetch({path: `ea/v1/settings`, method: 'POST', data}).then(res => {
			this.props.resetSettings('fetchAPI', 'settings');
		}).catch(error => {
			alert(error.message);
		});
	}

	render() {
		return (
			<Card>
				<Form
					onSubmit={this.onSubmit}
					initialValues={this.props.settings}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit}>
							<div className="ea-row">
								<Field
									label={__('Company Name', 'wp-ever-accounting')}
									name="company_name"
									className="ea-col-6"
									required>
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Company Email', 'wp-ever-accounting')}
									name="company_email"
									className="ea-col-6"
									required>
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Phone Number', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_phone">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Tax Number', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_tax_number">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Address', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_address">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('City', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_city">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('State', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_state">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Postcode', 'wp-ever-accounting')}
									className="ea-col-6"
									name="company_postcode">
									{props => (
										<TextControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Country', 'wp-ever-accounting')}
									name="company_country"
									className="ea-col-6"
									required>
									{props => (
										<CountryControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Logo', 'wp-ever-accounting')}
									name="logo"
									className="ea-col-12"
									required>
									{props => (
										<FileControl {...props.input} {...props}/>
									)}
								</Field>

							</div>
							<p>
								<Button isPrimary disabled={submitting || pristine} type="Submit">{__('Submit', 'wp-ever-accounting')}</Button>
							</p>
						</form>
					)}/>
			</Card>
		)
	}
}

export default compose(
	withSelect((select) => {
		const query = {section: 'general'};
		const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
		return {
			settings: fetchAPI('settings', query),
			isRequesting: isRequestingFetchAPI('settings', query),
		}
	}),
	withDispatch((dispatch) => {
		const {resetForSelectorAndResource} = dispatch('ea/collection');
		return {
			resetSettings: resetForSelectorAndResource
		}
	}),
	withPreloader(),
)(General)
