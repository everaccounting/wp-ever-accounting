/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { forwardRef, useMemo } from '@wordpress/element';

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( InputBase );
	const id = `input-base-control-${ instanceId }`;
	return idProp || id;
}

export function InputBase( props, ref ) {
	const {
		children,
		className,
		disabled = false,
		hideLabelFromVision = false,
		labelPosition,
		id: idProp,
		isFocused = false,
		label,
		prefix,
		size = 'default',
		suffix,
		...restProps
	} = props;
	const id = useUniqueId( idProp );
}

export default forwardRef( InputBase );
