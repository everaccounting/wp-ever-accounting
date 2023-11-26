/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { BaseControl } from '@wordpress/components';
import { useInstanceId } from '@wordpress/compose';
import { useState, forwardRef } from '@wordpress/element';

function useUniqueId(idProp) {
	const instanceId = useInstanceId( SelectControl );
	const id = `inspector-input-control-${ instanceId }`;

	return idProp || id;
}

function SelectControl( props ) {
	const { id: idProp, className, label, help } = props;
	const id = useUniqueId( idProp );
	const helpPropName = typeof help === 'string' ? 'aria-describedby' : 'aria-details';
	const helpProp = !! help ? { [ helpPropName ]: `${ id }__help` } : {};

	const classes = classNames( 'components-input-control', className );
	return (
		<BaseControl
			id={ id }
			className={ classes }
			label={ label }
			help={ help }
			__nextHasNoMarginBottom
		>
			<div className="components-input-control__input">Input</div>
		</BaseControl>
	);
}

export default SelectControl;
