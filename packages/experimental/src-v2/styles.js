/**
 * External dependencies
 */
import styled, { css } from 'styled-components';

export const StyledSelect = styled.div`
	position: relative;
	border-radius: 2px;
	cursor: pointer;
	font-size: 13px;
	min-height: 32px;
	align-items: center;
	box-sizing: border-box;
	display: flex;
	flex: 1 1 0;
	${ ( { variant } ) =>
		variant === 'empty'
			? css`
					display: inline-block;
			  `
			: css`
					width: 100%;
					border: 1px solid #949494;
					background: #fff;
					transition: background 0.1s;

					&:focus {
						box-shadow: 0 0 0 1px var( --eac--color-accent );
						outline: 2px solid transparent;
						outline-offset: -2px;
						border: 1px solid var( --eac--color-accent );
					}
			  ` }
`;

export const StyledSelect123 = styled( ( { variant, ...props } ) => <div { ...props } /> )( (
	props
) => {
	if ( props.variant === 'empty' ) {
		return css`
			display: inline-block;
		`;
	}

	return css`
		width: 100%;
		border: 1px solid #949494;
		background: #fff;
		transition: background 0.1s;

		&:focus {
			box-shadow: 0 0 0 1px var( --eac--color-accent );
			outline: 2px solid transparent;
			outline-offset: -2px;
			border: 1px solid var( --eac--color-accent );
		}
	`;
} );
