/**
 * External dependencies
 */
import { Form, Space, Spinner, Button, Modal, Text } from '@eac/components';
/**
 * WordPress dependencies
 */
import { Flex, FlexItem } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

function AddTransfer(props) {
	const {
		className,
		title = __('Add Transfer', 'wp-ever-accounting'),
		submitLabel = __('Add Transfer', 'wp-ever-accounting'),
		values: propValues = {},
		loading,
		onSubmit, // Custom onSubmit callback.
		onSuccess, // This callback is called when a record is saved successfully.
		onError, // This callback is called when a record is not saved successfully.
		beforeSubmit, // This callback is called before onSubmit prop is called.
		onClose, // This callback is called when the modal is closed.
		modalProps = {},
		formProps = {},
		children,
	} = props;

	const { saveRecord } = useDispatch('eac/entities');

	const onFormSubmit = async (values) => {
		return await saveRecord('payment', values);
	};

	return (
		<Form
			initialValues={propValues}
			className={className}
			validations={{
				date: Form.is.required(),
				account: Form.is.required(),
				amount: Form.is.required(),
				category: Form.is.required()
			}}
			onSubmit={async (values) => {
				try {
					if (beforeSubmit) {
						values = await beforeSubmit(values);
					}
					const result = await (onSubmit || onFormSubmit)(values);
					if (result) {
						onSuccess(result);
					}
				} catch (error) {
					onError?.(error);
				}
			}}
			{...formProps}
		>
			{({ dirty, isSubmitting, isValid, handleSubmit, values }) => (
				<Modal title={title} onRequestClose={onClose} {...modalProps} size="fill">
					<Spinner isActive={loading}>
						<Space size="medium" direction="vertical" style={{ display: 'flex' }}>

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="account"
										label={__('From Account', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select account', value: ''},

										]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="account"
										label={__('To Account', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select account', value: ''},

										]}
									/>
								</FlexItem>
							</Flex>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="account"
										label={__('From Account', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select account', value: ''},

										]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="date"
										label={__('Date', 'wp-ever-accounting')}
										placeholder={__('YYYY-MM-DD', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>
							<Form.Field.Input
								name="date"
								label={__('Date', 'wp-ever-accounting')}
								placeholder={__('YYYY-MM-DD', 'wp-ever-accounting')}
							/>

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="category"
										label={__('Category', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select category', value: ''},
											{ label: 'Deposit (#1)', value: '1' },
											{ label: 'Sales (#2)', value: '2' },
											{ label: 'Other (#3)', value: '3' },
											{ label: 'Withdrawal (#4)', value: '4' },
											{ label: 'Purchase (#5)', value: '5' },
											{ label: 'Uncategorized (#6)', value: '6' },
										]}

									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="customer"
										label={__('Customer', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select customer', value: '' },

										]}
									/>
								</FlexItem>
							</Flex>

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="invoice"
										label={__('Invoice', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select invoice', value: '' },

										]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="payment_method"
										label={__('Payment Method', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select payment method', value: 'US' },
											{ label: 'Cash', value: 'cash' },
											{ label: 'Cheque', value: 'check' },
											{ label: 'Credit Card', value: 'credit_card' },
											{ label: 'Debit Card', value: 'debit_card' },
											{ label: 'Bank Transfer', value: 'bank_transfer' },
											{ label: 'PayPal', value: 'paypal' },
											{ label: 'Other', value: 'other' },
										]}
									/>
								</FlexItem>
							</Flex>
							<hr />
							<Form.Field.Input
								name="reference"
								label={__('Reference', 'wp-ever-accounting')}
								placeholder={ __( 'Enter reference', 'wp-ever-accounting' )}
							/>
							<Form.Field.Textarea
								name="notes"
								label={__('Notes', 'wp-ever-accounting')}
								placeholder={__( 'Enter description', 'wp-ever-accounting' )}
							/>

							{typeof children === 'function'
								? children({
									...props,
									dirty,
									isSubmitting,
									isValid,
									handleSubmit,
									values,
								})
								: children}
							<Button
								isPrimary
								onClick={handleSubmit}
								isBusy={isSubmitting}
								disabled={!isValid || isSubmitting || !dirty}
							>
								{submitLabel}
							</Button>
						</Space>
					</Spinner>
				</Modal>
			)}
		</Form>
	);
}

export default AddTransfer;
