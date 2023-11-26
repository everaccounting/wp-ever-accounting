/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { useState, useRef, forwardRef } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './style.scss';

const noop = () => {};

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( Select );
	const id = `inspector-select-control-${ instanceId }`;

	return idProp || id;
}

function UnforwardedSelect( props, ref ) {
	const { className, id: idProp, disabled = false, label, variant, prefix, ...restProps } = props;
	const id = useUniqueId( idProp );
	const selectRef = useRef();

	const classes = classnames( 'eac-select-control', className, {
		'eac-select-control--disabled': disabled,
		[ `eac-select-control--${ variant }` ]: variant,
	} );

	return (
		<BaseControl className={ classes } label={ label } id={ id } ref={ ref }>
			<div
				className="eac-select-control__container components-select-control__container"
				ref={ selectRef }
			>
			</div>
		</BaseControl>
	);
}

const Select = forwardRef( UnforwardedSelect );

export default Select;
