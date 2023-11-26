/**
 * External dependencies
 */
import { css } from '@emotion/react';
import styled from '@emotion/styled';

export const Container = styled.div`
	align-items: center;
	box-sizing: border-box;
	border-radius: inherit;
	display: flex;
	flex: 1;
	position: relative;
	width: 100%;
	background: ${ ( props ) => ( props.disabled ? '#f0f0f0' : '#fff' ) };
	z-index: ${ ( props ) => ( props.isFocused ? 1 : 0 ) };
`;

export const Prefix = styled.span`
	box-sizing: border-box;
	display: block;
`;

export const Suffix = styled.span`
	align-items: center;
	align-self: stretch;
	box-sizing: border-box;
	display: flex;
`;

const backdropFocusedStyles = ( { disabled, isFocused } ) => {
	let borderColor = isFocused
		? 'var(--wp-components-color-accent, var(--wp-admin-theme-color, #3858e9))'
		: '#949494';
	let boxShadow;
	let outline;
	let outlineOffset;
	if ( isFocused ) {
		boxShadow =
			'0 0 0 0.5px var(--wp-components-color-accent, var(--wp-admin-theme-color, #3858e9))';
		// Windows High Contrast mode will show this outline, but not the box-shadow.
		outline = `2px solid transparent`;
		outlineOffset = `-2px`;
	}
	if ( disabled ) {
		borderColor = '#ccc';
	}
	return css( {
		boxShadow,
		borderColor,
		borderStyle: 'solid',
		borderWidth: 1,
		outline,
		outlineOffset,
	} );
};

export const BackdropUI = styled.div`
	&&& {
		box-sizing: border-box;
		border-radius: inherit;
		bottom: 0;
		left: 0;
		margin: 0;
		padding: 0;
		pointer-events: none;
		position: absolute;
		right: 0;
		top: 0;

		${ backdropFocusedStyles }
	}
`;
