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

function AddVendor(props) {
	const {
		className,
		title = __('Add Vendor', 'wp-ever-accounting'),
		submitLabel = __('Add Vendor', 'wp-ever-accounting'),
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
		return await saveRecord('vendor', values);
	};

	return (
		<Form
			initialValues={propValues}
			className={className}
			validations={{
				name: Form.is.required(),
				type: Form.is.required(),
				currency_code: Form.is.required()
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
								placeholder={__('John Doe', 'wp-ever-accounting')}
							/>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Select
										name="currency_code"
										label={__('Currency Code', 'wp-ever-accounting')}
										__nextMarginBottom="0"
										options={[
											{ label: 'Select an option...', value: '' },
											{ label: 'US Dollar (USD)', value: 'USD' },
										]}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="phone"
										label={__('Phone', 'wp-ever-accounting')}
										placeholder={__( '+11234567890', 'wp-ever-accounting' )}
									/>
								</FlexItem>
							</Flex>
							<Form.Field.Input
								name="email"
								label={__('Email', 'wp-ever-accounting')}
								placeholder={__( 'john@company.com', 'wp-ever-accounting' )}
							/>

							<hr />

							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="company"
										label={__('Company', 'wp-ever-accounting')}
										placeholder={__( 'XYZ Inc.', 'wp-ever-accounting' )}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="website"
										label={__('Website', 'wp-ever-accounting')}
										placeholder={__( 'https://example.com', 'wp-ever-accounting' )}
									/>
								</FlexItem>
							</Flex>
							<Form.Field.Input
								name="vat_number"
								label={__('VAT Number', 'wp-ever-accounting')}
								placeholder={__( '123456789', 'wp-ever-accounting' )}
							/>
							<hr />
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="address1"
										label={__('Address Line 1', 'wp-ever-accounting')}
										placeholder={__( '123 Main St', 'wp-ever-accounting' )}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="address2"
										label={__('Address Line 2', 'wp-ever-accounting')}
										placeholder={__( 'Apartment, studio, or floor', 'wp-ever-accounting' )}
									/>
								</FlexItem>
							</Flex>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="city"
										label={__('City', 'wp-ever-accounting')}
										placeholder={__( 'New York', 'wp-ever-accounting' )}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="state"
										label={__('State', 'wp-ever-accounting')}
										placeholder={__( 'NY', 'wp-ever-accounting' )}
									/>
								</FlexItem>
							</Flex>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="postcode"
										label={__('Postcode', 'wp-ever-accounting')}
										placeholder={ __( '10001', 'wp-ever-accounting' )}
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

export default AddVendor;
