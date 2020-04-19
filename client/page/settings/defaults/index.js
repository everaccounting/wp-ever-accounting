import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withSelect, withDispatch} from "@wordpress/data";
import {withPreloader} from "@eaccounting/hoc";
import {compose} from '@wordpress/compose';
import {Form, Field, FormSpy} from "react-final-form";
import {
	Button,
	FormCard,
	AccountSelect,
	CurrencySelect,
	PaymentMethodSelect
} from "@eaccounting/components";
import apiFetch from "@wordpress/api-fetch";
import {NotificationManager} from "react-notifications";

import {get, pickBy, isObject} from "lodash";

const processFormData = (data) => (pickBy({
	...data,
	default_account_id: get(data, 'default_account.id'),
	default_currency_id: get(data, 'default_currency.id')
}, value => !isObject(value)));


class Defaults extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(data) {
		data.section = 'defaults';
		apiFetch({path: `ea/v1/settings`, method: 'POST', data}).then(res => {
			this.props.resetSettings('fetchAPI', 'settings');
			NotificationManager.success(__('Settings Updated!'));
		}).catch(error => {
			NotificationManager.error(error.message);
		});
	}

	render() {
		return (
			<Fragment>
				<FormCard title={__('Defaults Settings')}>
					<Form
						onSubmit={(data) => this.onSubmit(processFormData(data))}
						initialValues={this.props.settings}
						render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">
									<Field
										label={__('Default Account', 'wp-ever-accounting')}
										name="default_account"
										className="ea-col-6"
										enableCreate={true}
										required>
										{props => (
											<AccountSelect {...props.input} {...props}/>
										)}
									</Field>
									<Field
										label={__('Default Currency', 'wp-ever-accounting')}
										name="default_currency"
										enableCreate={true}
										className="ea-col-6"
										required>
										{props => (
											<CurrencySelect {...props.input} {...props}/>
										)}
									</Field>
									{/*<Field*/}
									{/*	label={__('Default Tax Rate', 'wp-ever-accounting')}*/}
									{/*	className="ea-col-6"*/}
									{/*	name="default_tax_rate">*/}
									{/*	{props => (*/}
									{/*		<TaxRateControl {...props.input} {...props}/>*/}
									{/*	)}*/}
									{/*</Field>*/}
									<Field
										label={__('Default Payment Method', 'wp-ever-accounting')}
										className="ea-col-6"
										name="default_payment_method">
										{props => (
											<PaymentMethodSelect {...props.input} {...props}/>
										)}
									</Field>

								</div>
								<p>
									<Button
										isPrimary disabled={submitting || pristine}
										type="Submit">{__('Submit', 'wp-ever-accounting')}
									</Button>
								</p>

								<FormSpy subscription={{values: true}}>
									{({values}) => {
										values.default_tax_rate_id = values.default_tax_rate && values.default_tax_rate.id && values.default_tax_rate.id || 0;
										values.default_account_id = values.default_account && values.default_account.id && values.default_account.id || 0;
										values.default_currency_id = values.default_currency && values.default_currency.id && values.default_currency.id || 0;
										return null;
									}}
								</FormSpy>

							</form>
						)}/>
				</FormCard>
			</Fragment>
		)
	}
}

export default compose(
	withSelect((select) => {
		const query = {section: 'defaults'};
		const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
		return {
			settings: fetchAPI('settings', query),
			isLoading: isRequestingFetchAPI('settings', query),
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
