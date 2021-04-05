import {TextControl, Button} from "@wordpress/components";
import {Form} from "@eaccounting/components";

export default function FormDemo(){
	const initialValues = { firstName: '' };
	return(
		<>
		<h3>FORM</h3>
			<Form
				onSubmitCallback={ ( values ) => {} }
				initialValues={ initialValues }
			>
				{ ( {
						getInputProps,
						values,
						errors,
						handleSubmit,
					} ) => (
					<div>
						<TextControl
							label={ 'First Name' }
							{ ...getInputProps( 'firstName' ) }
						/>
						<Button
							isPrimary
							onClick={ handleSubmit }
							disabled={ Object.keys( errors ).length }
						>
							Submit
						</Button>
					</div>
				) }
			</Form>
		</>
	)
}
