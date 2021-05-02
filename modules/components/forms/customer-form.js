import Form from "../form";
import TextControl from "../text-control";
import { useDispatch, useSelect } from '@wordpress/data';
import {STORE_NAME} from "@eaccounting/data";
import {Button} from '@wordpress/components';

export default function CustomerForm(props){
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
					<>
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
					</>
				)}
			</Form>
		</>
	)
}
