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
	PriceControl
} from "@eaccounting/components";
import {Form, Field, FormSpy} from "react-final-form";


class ViewRevenue extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const {isAdd, item, settings} = this.props;
		const {default_account, default_payment_method} = settings;

		return (
			<FormCard title={isAdd ? __('Add Revenue') : __('Update Revenue')}>

				<ContactSelect include={[1]}/>

				{/*<Form*/}
				{/*	onSubmit={form => console.log(form)}*/}
				{/*	initialValues={item}*/}
				{/*	render={({submitError, handleSubmit, form, submitting, pristine, values}) => (*/}
				{/*		<form onSubmit={handleSubmit} className="ea-row">*/}

				{/*			<Field*/}
				{/*				label={__('Date', 'wp-ever-accounting')}*/}
				{/*				name="paid_at"*/}
				{/*				containerClass="ea-col-6"*/}
				{/*				required>*/}
				{/*				{props => (*/}
				{/*					<DateControl {...props.input} {...props}/>*/}
				{/*				)}*/}
				{/*			</Field>*/}
				{/*			*/}

				{/*			<Field*/}
				{/*				label={__('Account', 'wp-ever-accounting')}*/}
				{/*				name="account"*/}
				{/*				className="ea-col-6"*/}
				{/*				defaultValue={default_account}*/}
				{/*				after={values.account && values.account.currency_code && values.account.currency_code || null}*/}
				{/*				required>*/}
				{/*				{props => (*/}
				{/*					<AccountSelect {...props.input} {...props}/>*/}
				{/*				)}*/}
				{/*			</Field>*/}
				{/*			<Field*/}
				{/*				label={__('Amount', 'wp-ever-accounting')}*/}
				{/*				name="amount"*/}
				{/*				className="ea-col-6"*/}
				{/*				code={values.account && values.account.currency_code && values.account.currency_code || ''}*/}
				{/*				required>*/}
				{/*				{props => (*/}
				{/*					<PriceControl {...props.input} {...props}/>*/}
				{/*				)}*/}
				{/*			</Field>*/}

				{/*			<Field*/}
				{/*				label={__('Category', 'wp-ever-accounting')}*/}
				{/*				name="category"*/}
				{/*				className="ea-col-6"*/}
				{/*				required>*/}
				{/*				{props => (*/}
				{/*					<CategorySelect type="income" {...props.input} {...props}/>*/}
				{/*				)}*/}
				{/*			</Field>*/}

				{/*			/!*<Field*!/*/}
				{/*			/!*	label={__('Vendor', 'wp-ever-accounting')}*!/*/}
				{/*			/!*	name="contact"*!/*/}
				{/*			/!*	className="ea-col-6">*!/*/}
				{/*			/!*	{props => (*!/*/}
				{/*			/!*		<ContactControl {...props.input} {...props}/>*!/*/}
				{/*			/!*	)}*!/*/}
				{/*			/!*</Field>*!/*/}

				{/*			<Field*/}
				{/*				label={__('Payment Method', 'wp-ever-accounting')}*/}
				{/*				name="payment_method"*/}
				{/*				className="ea-col-6"*/}
				{/*				defaultValue={default_payment_method}*/}
				{/*				required>*/}
				{/*				{props => (*/}
				{/*					<PaymentMethodSelect {...props.input} {...props}/>*/}
				{/*				)}*/}
				{/*			</Field>*/}


				{/*		</form>*/}
				{/*	)}/>*/}

			</FormCard>
		);
	}
}

export default withEntity('revenues')(ViewRevenue);
