/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/components';

function useInnerClosable( closable, closeIcon, defaultClosable ) {
	if ( typeof closable === 'boolean' ) {
		return closable;
	}
	if ( closeIcon === undefined ) {
		return !! defaultClosable;
	}
	return closeIcon !== false && closeIcon !== null;
}
export function useClosable( closable, closeIcon, customCloseIconRender, defaultCloseIcon = <Icon icon="no-alt" />, defaultClosable = false ) {
	const mergedClosable = useInnerClosable( closable, closeIcon, defaultClosable );
	if ( ! mergedClosable ) {
		return [ false, null ];
	}
	const mergedCloseIcon = typeof closeIcon === 'boolean' || closeIcon === undefined || closeIcon === null ? defaultCloseIcon : closeIcon;
	return [ true, customCloseIconRender ? customCloseIconRender( mergedCloseIcon ) : mergedCloseIcon ];
}
