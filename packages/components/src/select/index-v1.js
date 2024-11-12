/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { forwardRef } from '@wordpress/element';
import { useState, useEffect, useRef } from '@wordpress/element';
import { chevronDown } from '@wordpress/icons';
import { BaseControl } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { useAutocomplete } from '../utils';

const Select = forwardRef( ( props, ref ) => {
	const {
		id,
		className,
		label,
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
		<BaseControl
			id={ id }
			label={ label }
			ref={ ref }
			{ ...getRootProps( { ...getInputLabelProps() } ) }
			className={ classnames( 'eac-select-control', className, {
				// 'is-read-only': isReadOnly,
				'is-focused': focused,
				'is-multiple': multiple,
				'has-selected-items': value && value.length,
			} ) }
		>
			<div
				ref={ setAnchorEl }
				className={ classnames( 'eac-select-control__combo-box-wrapper', {
					'eac-select-control__combo-box-wrapper--disabled': disabled,
				} ) }
			>
				<div className="eac-select-control__items-wrapper">
					{ multiple &&
						value.map( ( item, index ) => {
							const { key, ...tagProps } = getTagProps( { item, index } );
							return (
								<div key={ key } { ...tagProps }>
									{ getOptionLabel( item ) }
								</div>
							);
						} ) }
					<div className="eac-select-control__combox-box">
						<input { ...getInputProps() } />
					</div>
				</div>
			</div>
			<ul { ...getListBoxProps() }>
				{ options.map( ( option, index ) => {
					const { key, ...optionProps } = getOptionProps( { option, index } );
					return (
						<li key={ key } { ...optionProps }>
							{ getOptionLabel( option ) }
						</li>
					);
				} ) }
			</ul>
		</BaseControl>
	);
} );

export default Select;
