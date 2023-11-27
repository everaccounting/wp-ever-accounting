/**
 * External dependencies
 */
import { Form, Space, Spinner, Modal, Button } from '@eac/components';
/**
 * WordPress dependencies
 */
import { useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

function AddCategory(props) {
	const {
		className,
		title = __('Add Category', 'wp-ever-accounting'),
		submitLabel = __('Add Category', 'wp-ever-accounting'),
		values: formValues = {},
		showType = true,
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
		return await saveRecord('category', values);
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
								label={__('Name', 'wp-ever-accounting')}
								name="name"
								required
							/>
							<Form.Field.Input
								label={__('Description', 'wp-ever-accounting')}
								name="description"
								required
							/>
							{showType && (
								<Form.Field.Select
									label={__('Type', 'wp-ever-accounting')}
									name="type"
									required
									options={[
										{
											label: __('Income', 'wp-ever-accounting'),
											value: 'income',
										},
										{
											label: __('Expense', 'wp-ever-accounting'),
											value: 'expense',
										},
									]}
								/>
							)}
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

export default AddCategory;
