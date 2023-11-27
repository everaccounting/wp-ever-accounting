/**
 * External dependencies
 */
import { SectionHeader, Input, SelectControl } from '@eac/components';
import { AddCategory } from '@eac/editor';
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */

function List() {
	const [ addingCategory, setAddingCategory ] = useState( false );
	const handleAddCategory = (name) => {

	}
	return (
		<>
			<SectionHeader title={ __( 'List', 'wp-ever-accounting' ) } />
			<Input.AsyncSelect
				onCreateOption={ ( name ) => {
					return (
						<AddCategory
							name={ name }
							onSuccess={ () => setAddingCategory( false ) }
							onCancel={ () => setAddingCategory( false ) }
						/>
					);
				} }
				// disable creatable if adding category.

			/>
		</>
	);
}

export default List;
