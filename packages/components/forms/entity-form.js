import Form from "../form";
import { useDispatch, useSelect } from '@wordpress/data';
import {STORE_NAME} from "@eaccounting/data";
import {Button} from '@wordpress/components';

function EntityForm({entity = 'customer', item = {}, ...props }) {
	const { isSaving, saveError, entityName } = useSelect(
		(select) => {
			const {name} = select(STORE_NAME).getEntity(entity);
			return {
				entityName:name,
				isSaving: select(STORE_NAME).isSavingEntityRecord(name ),
				saveError: select(STORE_NAME).getLastEntitySaveError(name),
			};
		},
		[]
	);
	const { saveEntityRecord } = useDispatch( STORE_NAME );

	const onSubmitForm = (values) => {
		saveEntityRecord(entityName, {...item, ...values })
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
						<p>{props.children}</p>
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

export default EntityForm;
