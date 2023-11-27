/**
 * External dependencies
 */
import { Form, Space, Spinner, Button, Modal } from '@eac/components';
/**
 * WordPress dependencies
 */
import { Flex, FlexItem } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

function AddItem(props) {
	const {
		className,
		title = __('Add Item', 'wp-ever-accounting'),
		submitLabel = __('Add Item', 'wp-ever-accounting'),
		values: formValues = {},
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
		return await saveRecord('item', values);
	};

	return (
		<Form
			initialValues={formValues}
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
				<Modal title={title} onRequestClose={onClose} {...modalProps}>
					<Spinner isActive={loading}>
						<Space size="medium" direction="vertical" style={{ display: 'flex' }}>
							<Form.Field.Input
								name="name"
								label={__('Name', 'wp-ever-accounting')}
							/>
							<Flex isBlock>
								<FlexItem isBlock>
									<Form.Field.Input
										name="sale_price"
										label={__('Sale Price', 'wp-ever-accounting')}
									/>
								</FlexItem>
								<FlexItem isBlock>
									<Form.Field.Input
										name="purchase_price"
										label={__('Purchase Price', 'wp-ever-accounting')}
									/>
								</FlexItem>
							</Flex>
							<Form.Field.Input
								name="category"
								label={__('Category', 'wp-ever-accounting')}
							/>
							<Form.Field.Textarea
								name="description"
								label={__('Description', 'wp-ever-accounting')}
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

export default AddItem;
