import { Component, Fragment } from 'react';
import { __ } from '@wordpress/i18n';
import { withSelect, withDispatch } from '@wordpress/data';
import { withPreloader } from '@eaccounting/hoc';
import { compose } from '@wordpress/compose';
import { Form, Field, FormSpy } from 'react-final-form';
import {
	Card,
	Button,
	CompactCard,
	AccountSelect,
	CurrencySelect,
	TaxRateSelect,
	PaymentMethodSelect,
	TextControl,
} from '@eaccounting/components';
import apiFetch from '@wordpress/api-fetch';
import { NotificationManager } from 'react-notifications';

class Invoice extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(data) {
		data.section = 'invoice';
		apiFetch({ path: `ea/v1/settings`, method: 'POST', data })
			.then(res => {
				this.props.resetSettings('fetchAPI', 'settings');
				NotificationManager.success(__('Settings Updated!'));
			})
			.catch(error => {
				NotificationManager.error(error.message);
			});
	}

	render() {
		return (
			<Fragment>
				<CompactCard tagName="h3">{__('Invoice Settings')}</CompactCard>
				<Card>
					<Form
						onSubmit={this.onSubmit}
						initialValues={this.props.settings}
						render={({ submitError, handleSubmit, form, submitting, pristine, values }) => (
							<form onSubmit={handleSubmit}>
								<div className="ea-row">
									<Field
										label={__('Number Prefix', 'wp-ever-accounting')}
										name="invoice_number_prefix"
										className="ea-col-6"
										required
									>
										{props => <TextControl {...props.input} {...props} />}
									</Field>
									<Field
										label={__('Number Digit', 'wp-ever-accounting')}
										name="invoice_number_digit"
										className="ea-col-6"
										required
									>
										{props => <TextControl {...props.input} {...props} />}
									</Field>
								</div>

								<p>
									<Button isPrimary disabled={submitting || pristine} type="Submit">
										{__('Submit', 'wp-ever-accounting')}
									</Button>
								</p>
							</form>
						)}
					/>
				</Card>
			</Fragment>
		);
	}
}

export default compose(
	withSelect(select => {
		const query = { section: 'invoice' };
		const { fetchAPI, isRequestingFetchAPI } = select('ea/collection');
		return {
			settings: fetchAPI('settings', query),
			isLoading: isRequestingFetchAPI('settings', query),
		};
	}),
	withDispatch(dispatch => {
		const { resetForSelectorAndResource } = dispatch('ea/collection');
		return {
			resetSettings: resetForSelectorAndResource,
		};
	}),
	withPreloader()
)(Invoice);
