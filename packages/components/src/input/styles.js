/**
 * External dependencies
 */
import { css } from '@emotion/react';
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { Flex, FlexItem } from '@wordpress/components';

const GRID_BASE = '4px';

export function space( value ) {
	if ( ! value ) {
		return '0';
	}
	if (
		( typeof window !== 'undefined' && window.CSS?.supports?.( 'margin', value.toString() ) ) ||
		Number.isNaN( Number( value ) )
	) {
		return value.toString();
	}
	return `calc(${ GRID_BASE } * ${ value })`;
}

export const getSizeConfig = ( size ) => {
	// Paddings may be overridden by the custom paddings props.
	const sizes = {
		default: {
			height: 40,
			lineHeight: 1,
			minHeight: 40,
			paddingLeft: space( 4 ),
			paddingRight: space( 4 ),
		},
		small: {
			height: 24,
			lineHeight: 1,
			minHeight: 24,
			paddingLeft: space( 2 ),
			paddingRight: space( 2 ),
		},
	};
	return sizes[ size ] || sizes.default;
};

const rootFocusedStyles = ( { isFocused } ) => {
	if ( ! isFocused ) {
		return '';
	}
	return css( { zIndex: 1 } );
};
export const Root = styled( Flex )`
	box-sizing: border-box;
	position: relative;
	border-radius: 2px;
	padding-top: 0;
	${ rootFocusedStyles }
`;

const BaseLabel = styled( Text )`
	&&& {
		font-size: 11px;
		font-weight: 500;
		line-height: 1.4;
		text-transform: uppercase;
		box-sizing: border-box;
		display: block;
		padding-top: 0;
		padding-bottom: 0;
		max-width: 100%;
		z-index: 1;

		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
`;
export const Label = ( props ) => <BaseLabel { ...props } as="label" />;
export const LabelWrapper = styled( FlexItem )`
	max-width: calc( 100% - 10px );
`;
const containerDisabledStyles = ( { disabled } ) => {
	const backgroundColor = disabled ? '#f0f0f0' : '#fff';
	return css( { backgroundColor } );
};
const containerWidthStyles = ( { inputWidth, labelPosition } ) => {
	if ( ! inputWidth ) {
		return css( { width: '100%' } );
	}
	if ( labelPosition === 'side' ) {
		return '';
	}
	if ( labelPosition === 'edge' ) {
		return css( {
			flex: `0 0 ${ inputWidth }`,
		} );
	}
	return css( { width: inputWidth } );
};

export const Container = styled.div`
	align-items: center;
	box-sizing: border-box;
	border-radius: inherit;
	display: flex;
	flex: 1;
	position: relative;

	${ containerDisabledStyles }
	${ containerWidthStyles }
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
