import { Component, Fragment } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { withEntity } from '@eaccounting/hoc';
import {
	AccountSelect,
	DateControl,
	FormCard,
	PaymentMethodSelect,
	TextareaControl,
	PriceControl,
	Button,
	TextControl,
	BackButton,
} from '@eaccounting/components';
import { Form, Field } from 'react-final-form';
import { get, pickBy, isObject } from 'lodash';
import { NotificationManager } from 'react-notifications';

const processFormData = data =>
	pickBy(
		{
			...data,
			from_account_id: get(data, 'from_account.id'),
			to_account_id: get(data, 'to_account.id'),
		},
		value => !isObject(value)
	);

class EditTransfer extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(form) {
		const { history, isAdd } = this.props;
		this.props.handleSubmit(
			form,
			function(res) {
				NotificationManager.success(sprintf(__('Transfer %s.'), isAdd ? __('created') : __('updated')));
				history.push('/banking/transfers');
			},
			true
		);
	}

	render() {
		const { isAdd, item, settings } = this.props;
		const { default_account, default_payment_method } = settings;
		return (
			<FormCard title={isAdd ? __('Add Transfer') : __('Update Transfer')}>
				<Form
					onSubmit={data => this.onSubmit(processFormData(data))}
					initialValues={item}
					render={({ submitError, handleSubmit, form, submitting, pristine, values }) => (
						<form onSubmit={handleSubmit} className="ea-row">
							<Field
								label={__('From Account', 'wp-ever-accounting')}
								name="from_account"
								defaultValue={default_account}
								className="ea-col-6"
								after={get(values, 'from_account.currency_code')}
								disabledOption={get(values, 'to_account', {})}
								help={
									get(values, 'from_account.balance', false)
										? sprintf('Account balance is %s', get(values, 'from_account.balance', '0'))
										: ''
								}
								required
							>
								{props => <AccountSelect {...props.input} {...props} />}
							</Field>
							<Field
								label={__('To Account', 'wp-ever-accounting')}
								name="to_account"
								className="ea-col-6"
								after={get(values, 'to_account.currency_code')}
								disabledOption={get(values, ['from_account'], {})}
								help={
									get(values, 'from_account.balance', false)
										? sprintf('Account balance is %s', get(values, 'from_account.balance', '0'))
										: ''
								}
								required
							>
								{props => <AccountSelect {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Amount', 'wp-ever-accounting')}
								name="amount"
								className="ea-col-6"
								defaultValue={0}
								code={get(values, 'from_account.currency_code')}
								required
							>
								{props => <PriceControl {...props.input} {...props} />}
							</Field>

							<Field label={__('Date', 'wp-ever-accounting')} name="transferred_at" containerClass="ea-col-6" required>
								{props => <DateControl {...props.input} {...props} />}
							</Field>

							<Field
								label={__('Payment Method', 'wp-ever-accounting')}
								name="payment_method"
								className="ea-col-6"
								defaultValue={default_payment_method}
								required
							>
								{props => <PaymentMethodSelect {...props.input} {...props} />}
							</Field>

							<Field label={__('Reference', 'wp-ever-accounting')} name="reference" className="ea-col-6">
								{props => <TextControl {...props.input} {...props} />}
							</Field>

							<Field label={__('Description', 'wp-ever-accounting')} className="ea-col-12" name="description">
								{props => <TextareaControl {...props.input} {...props} />}
							</Field>

							<p className="ea-col-12">
								<Button isPrimary disabled={submitting || pristine} type="submit">
									{__('Submit')}
								</Button>

								<BackButton>{__('Cancel')}</BackButton>
							</p>
						</form>
					)}
				/>
			</FormCard>
		);
	}
}

export default withEntity('transfers')(EditTransfer);
