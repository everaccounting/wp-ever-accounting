import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withSelect, withDispatch} from "@wordpress/data";
import {withPreloader} from "@eaccounting/hoc/";
import {compose} from '@wordpress/compose';
import {Form, Field} from "react-final-form";
import {
	Card,
	Button,
	CurrencyControl,
	TaxRateControl,
	AccountControl,
	PaymentMethodControl
} from "@eaccounting/components";
import apiFetch from "@wordpress/api-fetch";

class Defaults extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(data){
		data.default_tax_rate_id = data.default_tax_rate && data.default_tax_rate.id && data.default_tax_rate.id || 0;
		data.default_account_id = data.default_account && data.default_account.id && data.default_account.id || 0;
		data.default_currency_id = data.default_currency && data.default_currency.id && data.default_currency.id || 0;
		data.section = 'defaults';
		delete data.default_tax_rate;
		delete data.default_account;
		delete data.default_currency;
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
									label={__('Default Account', 'wp-ever-accounting')}
									name="default_account"
									className="ea-col-6"
									required>
									{props => (
										<AccountControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Default Currency', 'wp-ever-accounting')}
									name="default_currency"
									className="ea-col-6"
									required>
									{props => (
										<CurrencyControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Default Tax Rate', 'wp-ever-accounting')}
									className="ea-col-6"
									name="default_tax_rate">
									{props => (
										<TaxRateControl {...props.input} {...props}/>
									)}
								</Field>
								<Field
									label={__('Default Payment Method', 'wp-ever-accounting')}
									className="ea-col-6"
									name="default_payment_method">
									{props => (
										<PaymentMethodControl {...props.input} {...props}/>
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
		const query = {section: 'defaults'};
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
)(Defaults)
