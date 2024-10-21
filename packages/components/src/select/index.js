/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { forwardRef } from '@wordpress/element';
import { useState, useEffect, useRef } from '@wordpress/element';
import { chevronDown, Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { useAutocomplete } from '../utils';
import { StyledLabel, StyledHelp } from './styles';

const Tag = ( props ) => {
	const { label, onDelete, ...other } = props;
	return (
		<div className="eac-select-control__selected-item-tag" { ...other }>
			<span className="eac-select-control__selected-item-tag-label">{ label }</span>
			<button
				type="button"
				className="eac-select-control__selected-item-tag-remove"
				onClick={ onDelete }
			></button>
		</div>
	);
};

const Select = forwardRef( ( props, ref ) => {
	const {
		id,
		className,
		label,
		help,
		options,
		value,
		onChange,
		disabled,
		placeholder,
		multiple,
		loading,
		getOptionLabel,
		getOptionValue,
		focused,
		setAnchorEl,
		getRootProps,
		getInputLabelProps,
		getInputProps,
		getListBoxProps,
		getOptionProps,
		getTagProps,
	} = useAutocomplete( props );

	return (
		<div
			id={ id }
			ref={ ref }
			{ ...getRootProps() }
			className={ classnames( 'eac-select-control', className, {
				'is-focused': focused,
				'is-multiple': multiple,
				'has-selected-items': value && value.length,
			} ) }
		>
			{ label && <StyledLabel { ...getInputLabelProps() }>{ label }</StyledLabel> }

			<div
				className={ classnames( 'eac-select-control__combo-box-wrapper', {
					'eac-select-control__combo-box-wrapper--disabled': disabled,
				} ) }
			>
				<div className="eac-select-control__items-wrapper">
					{ multiple && value && (
						<div className="eac-select-control__selected-items">
							{ value.map( ( item, index ) => (
								<Tag
									key={ index }
									className="eac-select-control__selected-item"
									{ ...getTagProps( { index } ) }
									label={ getOptionLabel( item ) }
								/>
							) ) }
						</div>
					) }
					<div className="eac-select-control__combox-box">
						<input className="eac-select-control__input" { ...getInputProps() } />
					</div>
				</div>
				<div className="eac-select-control__popover-menu">
					<ul { ...getListBoxProps() } className="eac-select-control__menu">
						{ options.map( ( option, index ) => (
							<li
								key={ index }
								{ ...getOptionProps( { option, index } ) }
								className="eac-select-control__menu-item"
							>
								{ getOptionLabel( option ) }
							</li>
						) ) }
					</ul>
				</div>
				<div className="eac-select-control__suffix-icon">
					<Icon icon={ chevronDown } size={ 24 } />
				</div>
			</div>

			{ help && <StyledHelp id={ `${ id }-help` }>{ help }</StyledHelp> }
		</div>
	);
} );

export default Select;
