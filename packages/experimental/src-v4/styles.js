/**
 * External dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { css } from '@emotion/react';
// eslint-disable-next-line import/no-extraneous-dependencies
import styled from '@emotion/styled';

const variantStyles = ({ variant }) => {
	if (variant === 'empty') {
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
			box-shadow: 0 0 0 1px var(--eac--color-accent);
			outline: 2px solid transparent;
			outline-offset: -2px;
			border: 1px solid var(--eac--color-accent);
		}
	`;
};

export const SelectContainer = styled.div`
	position: relative;
	border-radius: 2px;
	cursor: pointer;
	//min-height: 32px;
	align-items: center;
	box-sizing: border-box;
	display: flex;
	flex: 1 1 0;
	font-size: 13px;
	line-height: normal;
	${variantStyles}
`;

export const MenuContainer = styled.div`
	z-index: 101;
	position: absolute;
	top: 100%;
	left: 0;
	border-radius: 0 0 4px 4px;
	background: #fff;
	box-shadow:
		rgba(9, 30, 66, 0.25) 0px 4px 8px -2px,
		rgba(9, 30, 66, 0.31) 0px 0px 1px;
	${(props) => (props.width ? `width: ${props.width}px;` : 'width: 100%;')}
`;

export const SearchInput = styled.input`
	padding: 10px 14px 8px;
	width: 100%;
	border: none !important;
	background: none;
	border-radius: 0 !important;
	outline: none !important;
	box-shadow: none !important;

	&:focus {
		outline: none;
	}
`;

export const Options = styled.div`
	max-height: 200px;
	overflow-x: hidden;
	overflow-y: auto;
	-webkit-overflow-scrolling: touch;
	&::-webkit-scrollbar {
		width: 8px;
	}
	&::-webkit-scrollbar-track {
		background: none;
	}

	&::-webkit-scrollbar-thumb {
		border-radius: 99px;
		background: #dfe1e6;
	}
`;

export const Option = styled.div`
	padding: 8px 14px;
	word-break: break-word;
	cursor: pointer;

	&:last-of-type {
		margin-bottom: 8px;
	}
	&[data-hover='true'] {
		background: var(--eac--color-accent);
		color: #fff;
	}
`;
