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
				name: Form.is.required(),
				type: Form.is.required(),
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
								name="name"
								label={__('Name', 'wp-ever-accounting')}
							/>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="email"
										label={__('Email', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="phone"
										label={__('Phone', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>

							<hr />

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="company"
										label={__('Company', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="website"
										label={__('Website', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>
							<Form.Field.Input
								name="vat_number"
								label={__('VAT Number', 'wp-ever-accounting')}
							/>
							<hr />
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="address1"
										label={__('Address Line 1', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="address2"
										label={__('Address Line 2', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="city"
										label={__('City', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="state"
										label={__('State', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="postcode"
										label={__('Postcode', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Select
										name="country"
										label={__('Country', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'United States', value: 'US' },
											{ label: 'United Kingdom', value: 'UK' },
										]}
									/>
								</FlexItem>
							</Flex>
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
