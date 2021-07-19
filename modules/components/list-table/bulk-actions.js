/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
import { SelectControl, Button } from '@wordpress/components';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { find, isEmpty, noop } from 'lodash';
import { __ } from '@wordpress/i18n';

function BulkActions( { selectedItems = [], actions, onAction } ) {
	const [ action, setAction ] = useState( '' );
	const placeholder = { value: '', label: __( 'Bulk Actions' ) };
	if ( ! find( actions, placeholder ) ) {
		actions.unshift( placeholder );
	}

	const click = async ( e ) => {
		e.preventDefault();
		await onAction( action, selectedItems );
		setAction( '' );
	};

	return (
		<div className="alignleft actions bulkactions">
			<SelectControl
				className="select"
				disabled={ isEmpty( selectedItems ) }
				value={ action }
				style={ {
					display: 'inline-block',
					marginRight: '6px',
				} }
				onChange={ setAction }
				options={ actions }
			/>
			<Button
				className="button action"
				disabled={ isEmpty( selectedItems ) || ! action }
				onClick={ click }
			>
				{ __( 'Apply' ) }
			</Button>
		</div>
	);
}

BulkActions.propTypes = {
	actions: PropTypes.arrayOf(
		PropTypes.shape( {
			label: PropTypes.string,
			value: PropTypes.string,
		} )
	),
	onAction: PropTypes.func,
	selectedItems: PropTypes.array,
};

BulkActions.defaultProps = {
	onAction: noop,
};

export default BulkActions;
