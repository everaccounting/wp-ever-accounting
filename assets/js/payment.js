/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';
import { Form } from '@eaccounting/components';
import { useApi, api } from '@eaccounting/data';

export const Payment = ( { payment = {}, fetchPayment, onSave } ) => {
	const loadAccounts = async ( search ) => {
		const res = await api.get( '/ea/v1/accounts', { search } );
		const { data = [] } = res;
		return data;
	};
	return (
		<>
			<h1>Payment</h1>
			Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus,
			molestiae.
			<Form
				enableReinitialize
				initialValues={ {
					validateOnMount: true,
					account: payment.account,
					payment_date: payment.payment_date,
					payment_method: payment.payment_method || {},
					amount: payment.amount,
					note: payment.note,
				} }
				onSubmit={ ( values ) => {
					console.log( values );
				} }
			>
				<div>
					<pre>{ JSON.stringify( payment, null, 2 ) }</pre>
					<Form.Field.Select
						isAsync
						loadOptions={ loadAccounts }
						name="account"
						label="Account"
						defaultOptions={ [
							{
								value: '',
								label: 'Select Account',
							},
						] }
						getOption={ ( option ) => {
							return {
								value: option.id,
								label: option.name,
							};
						} }
					/>
					<Form.Field.Input name="amount" label="Amount" />
					<Form.Field.Select
						name="payment_method"
						label="Payment Method"
						options={ [
							{
								value: 'cash',
								label: 'Cash',
							},
							{
								value: 'bank_transfer',
								label: 'Bank Transfer',
							},
							{
								value: 'cheque',
								label: 'Cheque',
							},
						] }
					/>
				</div>
			</Form>
		</>
	);
};

export default Payment;
