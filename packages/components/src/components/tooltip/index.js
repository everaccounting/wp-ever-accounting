/**
 * WordPress dependencies
 */
import { Tooltip as NativeTooltip } from '@wordpress/components';
import { Icon, help } from '@wordpress/icons';
export const Tooltip = ( { children = <Icon icon={ help } />, ...props } ) => {
	return (
		<>
			<NativeTooltip { ...props }>{ children }</NativeTooltip>
		</>
	);
};

export default Tooltip;
