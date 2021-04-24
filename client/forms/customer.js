/**
 * External dependencies
 */
import { Form, TextControl } from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

export default function Customer({ item = {} }) {
	return (
		<>
			<Form onSubmitCallback={(values) => {}} initialValues={item}>
				{({ getInputProps, values, errors, handleSubmit }) => (
					<div>
						<TextControl
							label={'First Name'}
							{...getInputProps('firstName')}
						/>
						<Button
							isPrimary
							onClick={handleSubmit}
							disabled={Object.keys(errors).length}
						>
							Submit
						</Button>
					</div>
				)}
			</Form>
		</>
	);
}
