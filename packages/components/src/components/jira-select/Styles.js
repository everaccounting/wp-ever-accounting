/**
 * External dependencies
 */
import { css } from '@emotion/react';
// eslint-disable-next-line import/no-extraneous-dependencies
import styled from '@emotion/styled';

export const StyledIcon = styled.i`
	display: inline-block;
	font-size: ${(props) => `${props.size}px`};

	${(props) =>
		props.left || props.top ? `transform: translate(${props.left}px, ${props.top}px);` : ''}
	&:before {
		content: '${(props) => props.code}';
		font-family: 'jira' !important;
		speak: none;
		font-style: normal;
		font-weight: normal;
		font-variant: normal;
		text-transform: none;
		line-height: 1;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
`;

const fontIconCodes = {
	[`bug`]: '\\e90f',
	[`stopwatch`]: '\\e914',
	[`task`]: '\\e910',
	[`story`]: '\\e911',
	[`arrow-down`]: '\\e90a',
	[`arrow-left-circle`]: '\\e917',
	[`arrow-up`]: '\\e90b',
	[`chevron-down`]: '\\e900',
	[`chevron-left`]: '\\e901',
	[`chevron-right`]: '\\e902',
	[`chevron-up`]: '\\e903',
	[`board`]: '\\e904',
	[`help`]: '\\e905',
	[`link`]: '\\e90c',
	[`menu`]: '\\e916',
	[`more`]: '\\e90e',
	[`attach`]: '\\e90d',
	[`plus`]: '\\e906',
	[`search`]: '\\e907',
	[`issues`]: '\\e908',
	[`settings`]: '\\e909',
	[`close`]: '\\e913',
	[`feedback`]: '\\e918',
	[`trash`]: '\\e912',
	[`github`]: '\\e915',
	[`shipping`]: '\\e91c',
	[`component`]: '\\e91a',
	[`reports`]: '\\e91b',
	[`page`]: '\\e919',
	[`calendar`]: '\\e91d',
	[`arrow-left`]: '\\e91e',
	[`arrow-right`]: '\\e91f',
};

export const Icon = ({ type, ...iconProps }) => (
	<StyledIcon {...iconProps} data-testid={`icon:${type}`} code={fontIconCodes[type]} />
);

const color = {
	primary: '#0052cc', // Blue
	success: '#0B875B', // green
	danger: '#E13C3C', // red
	warning: '#F89C1C', // orange
	secondary: '#F4F5F7', // light grey

	textDarkest: '#172b4d',
	textDark: '#42526E',
	textMedium: '#5E6C84',
	textLight: '#8993a4',
	textLink: '#0052cc',

	backgroundDarkPrimary: '#0747A6',
	backgroundMedium: '#dfe1e6',
	backgroundLight: '#ebecf0',
	backgroundLightest: '#F4F5F7',
	backgroundLightPrimary: '#D2E5FE',
	backgroundLightSuccess: '#E4FCEF',

	borderLightest: '#dfe1e6',
	borderLight: '#C1C7D0',
	borderInputFocus: '#4c9aff',
};

const font = {
	regular: 'font-family: "CircularStdBook"; font-weight: normal;',
	medium: 'font-family: "CircularStdMedium"; font-weight: normal;',
	bold: 'font-family: "CircularStdBold"; font-weight: normal;',
	black: 'font-family: "CircularStdBlack"; font-weight: normal;',
	size: (size) => `font-size: ${size}px;`,
};

const mixin = {
	darken: (colorValue, amount) => Color(colorValue).darken(amount).string(),
	lighten: (colorValue, amount) => Color(colorValue).lighten(amount).string(),
	rgba: (colorValue, opacity) => Color(colorValue).alpha(opacity).string(),
	boxShadowMedium: css`
		box-shadow: 0 5px 10px 0 rgba(0, 0, 0, 0.1);
	`,
	boxShadowDropdown: css`
		box-shadow:
			rgba(9, 30, 66, 0.25) 0px 4px 8px -2px,
			rgba(9, 30, 66, 0.31) 0px 0px 1px;
	`,
	truncateText: css`
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	`,
	clickable: css`
		cursor: pointer;
		user-select: none;
	`,
	hardwareAccelerate: css`
		transform: translateZ(0);
	`,
	cover: css`
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
	`,
	placeholderColor: (colorValue) => css`
		::-webkit-input-placeholder {
			color: ${colorValue} !important;
			opacity: 1 !important;
		}

		:-moz-placeholder {
			color: ${colorValue} !important;
			opacity: 1 !important;
		}

		::-moz-placeholder {
			color: ${colorValue} !important;
			opacity: 1 !important;
		}

		:-ms-input-placeholder {
			color: ${colorValue} !important;
			opacity: 1 !important;
		}
	`,
	scrollableY: css`
		overflow-x: hidden;
		overflow-y: auto;
		-webkit-overflow-scrolling: touch;
	`,
	customScrollbar: ({ width = 8, background = color.backgroundMedium } = {}) => css`
		&::-webkit-scrollbar {
			width: ${width}px;
		}

		&::-webkit-scrollbar-track {
			background: none;
		}

		&::-webkit-scrollbar-thumb {
			border-radius: 99px;
			background: ${background};
		}
	`,
	backgroundImage: (imageURL) => css`
		background-image: url('${imageURL}');
		background-position: 50% 50%;
		background-repeat: no-repeat;
		background-size: cover;
		background-color: ${color.backgroundLight};
	`,
	link: (colorValue = color.textLink) => css`
		cursor: pointer;
		color: ${colorValue};

		${font.medium}
		&:hover, &:visited, &:active {
			color: ${colorValue};
		}

		&:hover {
			text-decoration: underline;
		}
	`,
	tag: (background = color.backgroundMedium, colorValue = color.textDarkest) => css`
		display: inline-flex;
		align-items: center;
		height: 24px;
		padding: 0 8px;
		border-radius: 4px;
		cursor: pointer;
		user-select: none;
		color: ${colorValue};
		background: ${background};

		${font.bold}
		${font.size(12)}
		i {
			margin-left: 4px;
		}
	`,
};

const zIndexValues = {
	modal: 1000,
	dropdown: 101,
	navLeft: 100,
};

export const StyledSelect = styled.div`
	position: relative;
	border-radius: 4px;
	cursor: pointer;

	${font.size(14)}
	${(props) => props.variant === 'empty' && `display: inline-block;`}
	${(props) =>
		props.variant === 'normal' &&
		css`
			width: 100%;
			border: 1px solid ${color.borderLightest};
			background: #fff;
			transition: background 0.1s;

			&:hover {
				//background: ${color.backgroundLight};
			}
		`}
	&:focus {
		outline: none;
		${(props) =>
			props.variant === 'normal' &&
			css`
				border: 1px solid ${color.borderInputFocus};
				box-shadow: 0 0 0 1px ${color.borderInputFocus};
				background: #fff;
			}
			`}
	}

	${(props) =>
		props.invalid &&
		css`
			&,
			&:focus {
				border: 1px solid ${color.danger};
				box-shadow: none;
			}
		`}
`;

export const ValueContainer = styled.div`
	display: flex;
	align-items: center;
	width: 100%;
	box-sizing: border-box;
	${(props) =>
		props.variant === 'normal' &&
		css`
			min-height: 32px;
			padding: 5px 5px 5px 10px;
		`}
`;

export const ChevronIcon = styled(Icon)`
	margin-left: auto;
	font-size: 18px;
	color: ${color.textMedium};
`;

export const Placeholder = styled.div`
	color: ${color.textLight};
`;

export const ValueMulti = styled.div`
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	${(props) => props.variant === 'normal' && `padding-top: 5px;`}
`;

export const ValueMultiItem = styled.div`
	margin: 0 5px 5px 0;
	${mixin.tag()}
`;

export const AddMore = styled.div`
	display: inline-block;
	margin-bottom: 3px;
	padding: 3px 0;

	${font.size(12.5)}
	${mixin.link()}
	i {
		margin-right: 3px;
		vertical-align: middle;
		font-size: 14px;
	}
`;

export const Dropdown = styled.div`
	z-index: ${zIndexValues.dropdown};
	position: absolute;
	top: 100%;
	left: 0;
	border-radius: 0 0 4px 4px;
	background: #fff;
	${mixin.boxShadowDropdown}
	${(props) => (props.width ? `width: ${props.width}px;` : 'width: 100%;')}
`;

export const DropdownInput = styled.input`
	padding: 10px 14px 8px;
	width: 100%;
	border: none;
	color: ${color.textDarkest};
	background: none;

	&:focus {
		outline: none;
	}
`;

export const ClearIcon = styled(Icon)`
	position: absolute;
	top: 4px;
	right: 7px;
	padding: 5px;
	font-size: 16px;
	color: ${color.textMedium};
	${mixin.clickable}
`;

export const Options = styled.div`
	max-height: 200px;
	${mixin.scrollableY};
	${mixin.customScrollbar()};
`;

export const Option = styled.div`
	padding: 8px 14px;
	word-break: break-word;
	cursor: pointer;

	&:last-of-type {
		margin-bottom: 8px;
	}

	&.jira-select-option-is-active {
		background: ${color.backgroundLightPrimary};
	}
`;

export const OptionsNoResults = styled.div`
	padding: 5px 15px 15px;
	color: ${color.textLight};
`;

