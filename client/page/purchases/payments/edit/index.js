import {Component, Fragment} from 'react';
import {__} from '@wordpress/i18n';
import {withEntity} from "@eaccounting/hoc";
import {
	AccountSelect,
	CategorySelect,
	ContactSelect,
	DateControl,
	FormCard,
	PaymentMethodSelect,
	TextareaControl,
	PriceControl,
	Button, TextControl, FileControl
} from "@eaccounting/components";
import {Form, Field} from "react-final-form";
import {get, pickBy, isObject} from "lodash";
import {NotificationManager} from "react-notifications";

const processFormData = (data) => (pickBy({
	...data,
	contact_id: get(data, 'contact.id'),
	category_id: get(data, 'category.id'),
	account_id: get(data, 'account.id'),
	file_id: get(data, 'file.id'),
}, value => !isObject(value)));


class EditPayment extends Component {
	constructor(props) {
		super(props);
		this.onSubmit = this.onSubmit.bind(this);
	}

	onSubmit(form) {
		const {history, isAdd} = this.props;
		this.props.handleSubmit(form, function (res) {
			NotificationManager.success(sprintf(__('Payment %s.'), isAdd ? __('created') : __('updated')));
			history.push('/purchases/payments');
		}, true );
	}

	render() {
		const {isAdd, item, settings} = this.props;
		const {default_account, default_payment_method} = settings;
		return (
			<FormCard title={isAdd ? __('Add Payment') : __('Update Payment')}>

				<Form
					onSubmit={data => this.onSubmit(processFormData(data))}
					initialValues={item}
					render={({submitError, handleSubmit, form, submitting, pristine, values}) => (
						<form onSubmit={handleSubmit} className="ea-row">

							<Field
								label={__('Date', 'wp-ever-accounting')}
								name="paid_at"
								containerClass="ea-col-6"
								required>
								{props => (
									<DateControl {...props.input} {...props}/>
								)}
							</Field>


							<Field
								label={__('Account', 'wp-ever-accounting')}
								name="account"
								className="ea-col-6"
								defaultValue={default_account}
								after={get(values, 'account.currency.code')}
								required>
								{props => (
									<AccountSelect create={true} {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Amount', 'wp-ever-accounting')}
								name="amount"
								className="ea-col-6"
								code={get(values, 'account.currency.code')}
								required>
								{props => (
									<PriceControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Vendor', 'wp-ever-accounting')}
								name="contact"
								className="ea-col-6"
								required>
								{props => (
									<ContactSelect create={true} type={'vendor'} {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Category', 'wp-ever-accounting')}
								name="category"
								className="ea-col-6"
								required>
								{props => (
									<CategorySelect create={true} type="expense" {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Payment Method', 'wp-ever-accounting')}
								name="payment_method"
								className="ea-col-6"
								defaultValue={default_payment_method}
								required>
								{props => (
									<PaymentMethodSelect {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Description', 'wp-ever-accounting')}
								name="description"
								parse={value => value}
								className="ea-col-12">
								{props => (
									<TextareaControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('Reference', 'wp-ever-accounting')}
								name="reference"
								parse={value => value}
								className="ea-col-6">
								{props => (
									<TextControl {...props.input} {...props}/>
								)}
							</Field>

							<Field
								label={__('File', 'wp-ever-accounting')}
								name="file"
								className="ea-col-6"
								accept={'image/*,.pdf,.doc,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'}
								hasButton={true}>
								{props => (
									<FileControl preview={false} {...props.input} {...props}/>
								)}
							</Field>


							<p className="ea-col-12">
								<Button
									isPrimary
									disabled={submitting || pristine}
									type="submit">{__('Submit')}
								</Button>
							</p>

						</form>
					)}/>

			</FormCard>
		);
	}
}

export default withEntity('payments')(EditPayment);
