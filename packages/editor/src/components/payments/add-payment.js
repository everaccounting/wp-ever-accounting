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

function AddPayment(props) {
	const {
		className,
		title = __('Add Payment', 'wp-ever-accounting'),
		submitLabel = __('Add Payment', 'wp-ever-accounting'),
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
							<Form.Field.Input
								name="date"
								label={__('Date', 'wp-ever-accounting')}
								placeholder={__('YYYY-MM-DD', 'wp-ever-accounting')}
							/>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="account"
										label={__('Select Account', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
										]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="amount"
										label={__('Amount', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
										]}
									/>
								</FlexItem>
							</Flex>

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="category"
										label={__('Category', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
										]}
									/>
								</FlexItem>
								 <FlexItem isBlock>
									<Form.Field.Select
										name="customer"
										label={__('Customer', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
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
										{ label: 'United States', value: 'US' },
										{ label: 'United Kingdom', value: 'UK' },
									]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="payment_method"
										label={__('Payment Method', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
										]}
									/>
								</FlexItem>
							</Flex>
							<hr />
							<Form.Field.Input
								name="reference"
								label={__('Reference', 'wp-ever-accounting')}
							/>
							<Form.Field.Textarea
								name="notes"
								label={__('Notes', 'wp-ever-accounting')}
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

export default AddPayment;
