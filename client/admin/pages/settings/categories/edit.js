/**
 * External dependencies
 */
import { useEntityRecord } from '@eac/data';
import { Result, Drawer, Form, Spinner } from '@eac/components';
import { navigate } from '@eac/navigation';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function Edit( { categoryId } ) {
	const category = useEntityRecord( 'item', categoryId );
	const { status, record } = category;

	const renderContent = () => {
		if ( status && status === 'error' ) {
			return (
				<Result
					status="error"
					title={ __( 'Error' ) }
					subTitle={ __( 'Something went wrong.' ) }
				/>
			);
		}

		if ( status && status === 'resolving' ) {
			return <Spinner />;
		}

		return (
			<Form
				enableReinitialize
				initialValues={ {
					...( record || {} ),
				} }
			>
				<>
					<Form.Field.Input label={ __( 'Name' ) } name="name" />
				</>
			</Form>
		);
	};

	return (
		<Drawer title={ __( 'Edit Category' ) } onClose={ () => navigate( {}, null, {} ) }>
			{ renderContent() }
		</Drawer>
	);
}

export default Edit;
