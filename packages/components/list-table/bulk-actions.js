import { useState } from '@wordpress/element';
import { SelectControl, Button } from '@wordpress/components';
import PropTypes from 'prop-types';
import { find } from 'lodash';
import { __, concat } from '@wordpress/i18n';

function BulkActions( { actions, isDisabled, onAction, value = '' } ) {
	const [ selected, setSelected ] = useState( value );
	const placeholder = { value: '', label: __( 'Bulk Actions' ) };
	if ( ! find( actions, placeholder ) ) {
		actions.unshift( placeholder );
	}

	const click = ( e ) => {
		e.preventDefault();
		selected && onAction && onAction( selected );
		setSelected( '' );
	};

	return (
		<div className="alignleft actions bulkactions">
			<SelectControl
				className="select"
				value={ selected }
				style={ {
					display: 'inline-block',
					marginRight: '6px',
				} }
				onChange={ setSelected }
				options={ actions }
			/>
			<button
				className="button action"
				disabled={ ! selected || !! isDisabled }
				onClick={ click }
			>
				{ __( 'Apply' ) }
			</button>
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
	isDisabled: PropTypes.bool,
};

export default BulkActions;
