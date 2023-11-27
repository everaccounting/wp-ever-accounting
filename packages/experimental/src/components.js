/**
 * External dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { css } from '@emotion/react';
// eslint-disable-next-line import/no-extraneous-dependencies
import styled from '@emotion/styled';

const variantStyles = ({ variant }) => {
	if (variant === 'inline') {
		return css`
			display: inline-block;
			max-width: 300px;
		`;
	}

	return css`
		width: 100%;
		border: 1px solid #949494;
		background-color: #ffffff;
	`;
};

const disabledStyles = ({ isDisabled }) => {
	if (isDisabled) {
		return css`
			background: #ddd;
			border-color: #ddd;
			cursor: not-allowed;
			opacity: 0.5;

			&:focus {
				box-shadow: none;
				outline: none;
			}
		`;
	}

	return css``;
};

export const StyledSelect = styled.div`
	${variantStyles}
	${disabledStyles}
`;

export const ValueContainer = styled.div`

`;
