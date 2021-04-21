import Form from "../form";
import TextControl from "../text-control";
import { useDispatch, useSelect } from '@wordpress/data';
import {STORE_NAME} from "@eaccounting/data";
import {Button} from '@wordpress/components';
function CategoryForm({onSave, item = {} }) {
	const { isSaving, saveError } = useSelect(
		(select) => {
			const {isSavingEntityRecord, getLastEntitySaveError} = select(STORE_NAME);
			return {
				isSaving: isSavingEntityRecord('category' ),
				saveError: getLastEntitySaveError('category'),
			};
		},
		[]
	);
	const { saveCategory } = useDispatch( STORE_NAME );

	const onSubmitForm = (values) => {
		saveCategory({...values, ...item});
		onSave && !saveError && onSave({...values, ...item})
	}

	return(
		<>

			<Form
				onSubmitCallback={onSubmitForm}
				initialValues={ item }
			>
				{({
					  getInputProps,
					  handleSubmit,
				  }) => (
					<div>
						{saveError && (
							<p>
								{saveError.message}
							</p>
						)}

						<TextControl
							label={'name'}
							required={true}
							{...getInputProps('name')}
						/>

						<Button
							isPrimary
							onClick={handleSubmit}
							isBusy={isSaving}>
							Submit
						</Button>
					</div>
				)}
			</Form>
		</>
	)
}

export default CategoryForm;
