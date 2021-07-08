/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import Select, { components } from 'react-select';
import Async from 'react-select/async';
/**
 * WordPress dependencies
 */
import { BaseControl } from '@wordpress/components';
import classnames from 'classnames';
import { withInstanceId } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import './style.scss';
function SelectControl( props ) {
	const {
		async = false,
		label,
		help,
		className,
		required,
		isMulti = false,
		before,
		after,
		setRef,
		instanceId,
		...restProps
	} = props;

	const classes = classnames(
		'ea-form-group',
		'ea-advance-select-wrap',
		className,
		{
			required: !! required,
		}
	);

	const selectorClasses = classnames( 'ea-advance-select', {
		has__before: !! before,
		has__after: !! after,
	} );

	const Control = async ? Async : Select;

	const id = `inspector-ea-input-group-${ instanceId }`;
	const describedby = [];
	if ( help ) {
		describedby.push( `${ id }__help` );
	}
	if ( before ) {
		describedby.push( `${ id }__before` );
	}
	if ( after ) {
		describedby.push( `${ id }__after` );
	}
	const SelectContainer = ( { children, ...containerProps } ) => {
		return (
			<components.SelectContainer { ...containerProps }>
				{ !! before && (
					<span
						id={ `${ id }__before` }
						className="ea-input-group__before"
					>
						{ before }
					</span>
				) }

				{ children }
				{ !! after && (
					<span
						id={ `${ id }__after` }
						className="ea-input-group__after"
					>
						{ after }
					</span>
				) }
			</components.SelectContainer>
		);
	};

	const DropdownIndicator = ( dropDownProps ) => {
		return <components.DropdownIndicator { ...dropDownProps } />;
	};

	return (
		<BaseControl
			id={ id }
			label={ label }
			help={ help }
			className={ classes }
		>
			<Control
				{ ...restProps }
				classNamePrefix="ea-advance-select"
				className={ selectorClasses }
				required={ required }
				isMulti={ isMulti }
				components={ { SelectContainer, DropdownIndicator } }
				ref={ setRef }
				aria-describedby={ describedby.join( ' ' ) }
				styles={ {
					menuPortal: ( base ) => ( { ...base, zIndex: 9999999 } ),
				} }
				menuPortalTarget={ document.getElementById(
					'eaccounting-root'
				) }
				menuPosition={ 'fixed' }
			/>
		</BaseControl>
	);
}

SelectControl.propTypes = {
	async: PropTypes.bool,
	className: PropTypes.string,
	label: PropTypes.string,
	name: PropTypes.string,
	clearable: PropTypes.bool,
	placeholder: PropTypes.string,
	searchable: PropTypes.bool,
	isMulti: PropTypes.bool,
	options: PropTypes.arrayOf( PropTypes.object ),
	disabledOption: PropTypes.object,
	value: PropTypes.any,
	onChange: PropTypes.func,
	onInputChange: PropTypes.func,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
	loadOptions: PropTypes.func,
};

export default withInstanceId( SelectControl );
