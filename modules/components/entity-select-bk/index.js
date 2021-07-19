/**
 * External dependencies
 */
import { STORE_NAME } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/components';
import PropTypes from 'prop-types';
import { get } from 'lodash';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { useState, useRef, cloneElement } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import SelectControl from '../select-control';

function EntitySelect( props ) {
	const {
		entityName,
		baseQuery = {},
		renderLabel,
		renderValue,
		valueKey,
		labelKey,
		creatable,
		modal,
		...restProps
	} = props;
	const [ isModalOpen, setModalOpen ] = useState( false );

	const { entityRecords, entityConfig } = useSelect(
		( select ) => {
			return {
				entityRecords: select( STORE_NAME ).getEntityRecords(
					entityName,
					baseQuery
				),
				entityConfig: select( STORE_NAME ).getEntity( entityName ),
			};
		},
		[ baseQuery ]
	);

	const entityRef = useRef();

	const fetchAPI = async ( params ) => {
		return await apiFetch( {
			path: addQueryArgs( entityConfig.endpoint, {
				...params,
				...baseQuery,
			} ),
		} );
	};

	const toggleModal = () => {
		setModalOpen( ! isModalOpen );
	};

	const handleCreate = async ( item ) => {
		await entityRef.current.select.select.setValue( item );
		toggleModal();
	};

	const onClick = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
		toggleModal();
	};

	const After = () => {
		const style = { cursor: 'pointer' };
		return <Icon icon="plus" style={ style } onClick={ onClick } />;
	};

	const getLabel = ( option ) => {
		if ( renderLabel ) {
			return renderLabel( option );
		}

		return get( option, [ labelKey ] );
	};

	const getValue = ( option ) => {
		if ( renderValue ) {
			return renderValue( option );
		}

		return get( option, [ valueKey ] );
	};

	return (
		<>
			<SelectControl
				{ ...restProps }
				async={ true }
				defaultOptions={ entityRecords }
				setRef={ entityRef }
				loadOptions={ ( search ) => fetchAPI( { search } ) }
				getOptionLabel={ getLabel }
				getOptionValue={ getValue }
				after={ creatable && modal && <After /> }
				noOptionsMessage={ ( input ) => {
					return input.inputValue
						? __( 'No Results', 'wp-ever-accounting' )
						: __( 'Type to search', 'wp-ever-accounting' );
				} }
			/>
			{ isModalOpen &&
				cloneElement( modal, {
					onClose: toggleModal,
					onSave: handleCreate,
					baseQuery,
				} ) }
		</>
	);
}

EntitySelect.propTypes = {
	entityName: PropTypes.string,
	renderLabel: PropTypes.func,
	renderValue: PropTypes.func,
	valueKey: PropTypes.string,
	labelKey: PropTypes.string,
	baseQuery: PropTypes.object,
	values: PropTypes.any,
	creatable: PropTypes.bool,
	modal: PropTypes.node,
};

EntitySelect.defaultProps = {
	valueKey: 'id',
	labelKey: 'name',
};
export default EntitySelect;
