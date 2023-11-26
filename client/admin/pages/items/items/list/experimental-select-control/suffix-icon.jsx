/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/icons';
export const SuffixIcon = ( { icon } ) => {
	return (
		<div className="woocommerce-experimental-select-control__suffix-icon">
			<Icon icon={ icon } size={ 24 } />
		</div>
	);
};
