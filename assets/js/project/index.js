/**
 * External dependencies
 */
// import React from 'react';
// import PropTypes from 'prop-types';

// import toast from '../utils/toast';
// import useApi from 'shared/hooks/api';
import { Form } from '@eaccounting/components';
/**
 * Internal dependencies
 */
import { FormCont, FormHeading, FormElement, ActionButton } from './Styles';

export const ProjectCategory = {
	SOFTWARE: 'software',
	MARKETING: 'marketing',
	BUSINESS: 'business',
};

export const ProjectCategoryCopy = {
	[ ProjectCategory.SOFTWARE ]: 'Software',
	[ ProjectCategory.MARKETING ]: 'Marketing',
	[ ProjectCategory.BUSINESS ]: 'Business',
};

/**
 * Internal dependencies
 */
// import { FormCont, FormHeading, FormElement, ActionButton } from './Styles';

const propTypes = {
	// project: PropTypes.object.isRequired,
	// fetchProject: PropTypes.func.isRequired,
};

const ProjectSettings = ( { project, fetchProject } ) => {
	// const [ { isUpdating }, updateProject ] = useApi.put( '/project' );
	// console.log(project)
	return (
		<Form
			initialValues={ Form.initialValues( project, ( get ) => ( {
				type: get( 'type' ),
				link: get( 'link' ),
				category: get( 'category' ),
				description: get( 'description' ),
			} ) ) }
			validations={ {
				name: [ Form.is.required(), Form.is.maxLength( 100 ) ],
				link: Form.is.url(),
				category: Form.is.required(),
			} }
			onSubmit={ async ( values, form ) => {
				console.log( values );
				console.log( form );
				// try {
				// 	await updateProject( values );
				// 	await fetchProject();
				// 	toast.success( 'Changes have been saved successfully.' );
				// } catch ( error ) {
				// 	Form.handleAPIError( error, form );
				// }
			} }
			enableReinitialize={ true }
		>
			<div>
				<Form.Field.Input name="type" label="Type" />
				<Form.Field.Input name="link" label="URL" />
				<ActionButton type="submit" variant="primary">
					Save changes
				</ActionButton>
			</div>
		</Form>
	);
};

ProjectSettings.propTypes = propTypes;

export default ProjectSettings;
