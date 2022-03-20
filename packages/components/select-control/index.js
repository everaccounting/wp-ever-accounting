/**
 * External dependencies
 */
import ReactSelect, {
	components as selectComponents,
	ActionMeta,
	OnChangeValue,
} from 'react-select';
import AsyncReactSelect from 'react-select/async';
import CreatableReactSelect from 'react-select/creatable';
/**
 * WordPress dependencies
 */
import { cloneElement, forwardRef } from '@wordpress/element';
import { BaseControl, Icon } from '@wordpress/components';

// Custom styles for components which do not have a chakra equivalent
const wpStyles = {
	// When disabled, react-select sets the pointer-state to none
	// which prevents the `not-allowed` cursor style from chakra
	// from getting applied to the Control
	container: ( base ) => ( {
		...base,
		pointerEvents: 'auto',
		' .eaccounting_select__indicator': {
			padding: '0',
		},
	} ),
	input: ( base ) => ( {
		...base,
		color: 'inherit',
		fontSize: '13px',
		minHeight: '24px',
		minWidth: '50px',
		boxShadow: 'none',
		flex: '1',
		'> input': {
			minHeight: 'auto',
			boxShadow: 'none !important',
		},
	} ),
	menu: ( base ) => ( {
		...base,
		marginTop: '0',
		marginBottom: '0',
		borderRadius: '0',
		boxSizing: 'border-box',
	} ),
	menuList: ( base ) => ( {
		...base,
		padding: '0',
		borderRadius: '0',
		boxSizing: 'border-box',
	} ),
	valueContainer: ( base ) => ( {
		...base,
		padding: '0',
	} ),
	indicator: ( base ) => ( {
		...base,
		padding: '0',
		size: '18px',
	} ),
	indicatorsContainer: ( base ) => ( {
		...base,
		padding: '0',
		' .eaccounting_select__indicator': {
			padding: '0 !important',
		},
	} ),
	control: ( base, state ) => ( {
		...base,
		borderColor: '#757575',
		minHeight: '24px',
		fontSize: '13px',
		margin: '0 0 8px 0',
		padding: '0px 4px',
		borderRadius: '2px',
		...( state.menuIsOpen
			? {
					borderBottomColor: 'transparent',
			  }
			: {} ),
	} ),
	option: ( base, state ) => ( {
		...base,
		fontSize: '13px',
		padding: '4px 8px',
		color: state.isFocused || state.isSelected ? '#fff' : 'inherit',
		// color: state.isSelected ? '#fff' : 'blue',
		margin: 0,
		'&:hover,&:focus': {
			backgroundColor: 'var(--wp-admin-theme-color)',
			color: '#fff',
		},
	} ),
	multiValue: ( base ) => ( {
		...base,
		backgroundColor: '#ddd',
		lineHeight: '24px',
		margin: '2px 4px 2px 0',
	} ),
	multiValueLabel: ( base ) => ( {
		...base,
		borderRadius: '2px 0 0 2px',
		padding: '0 0 0 8px',
		whiteSpace: 'nowrap',
		overflow: 'hidden',
		textOverflow: 'ellipsis',
	} ),
	multiValueRemove: ( base ) => ( {
		...base,
		height: '24px',
		width: '24px',
		padding: '0 2px',
		borderRadius: '0 2px 2px 0',
		lineHeight: '10px',
		justifyContent: 'center',
		cursor: 'pointer',
		'&:hover': {
			backgroundColor: 'inherit',
		},
	} ),
	// group: () => ( {} ),
};
//
const wpComponents = {
	IndicatorSeparator: () => null,
	DropdownIndicator: ( innerProps ) => {
		return (
			<selectComponents.DropdownIndicator { ...innerProps } size={ 20 }>
				<Icon
					icon={ () => (
						<svg
							viewBox="0 0 24 24"
							xmlns="http://www.w3.org/2000/svg"
							width="18"
							height="18"
							role="img"
							aria-hidden="true"
							focusable="false"
						>
							<path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" />
						</svg>
					) }
				/>
			</selectComponents.DropdownIndicator>
		);
	},
};

function flatten( arr ) {
	return arr.reduce(
		( acc, val ) =>
			Array.isArray( val.options )
				? acc.concat( flatten( val.options ) )
				: acc.concat( val ),
		[]
	);
}

const clean = ( x ) => x.trim();
const toArray = ( str ) =>
	typeof str === 'string' ? str.split( ',' ).map( clean ) : str;

function getValue( opts, val, getOptVal, isMulti ) {
	if ( val === undefined ) return undefined;

	const options = flatten( opts );
	return isMulti
		? opts.filter( ( o ) => toArray( val ).includes( getOptVal( o ) ) )
		: options.find( ( o ) => getOptVal( o ) === val );
}

// Component

const defaultGetOptionValue = ( opt ) => opt.value;

const SelectControl = ( {
	children,
	styles = {},
	components = {},
	isDisabled,
	isInvalid,
	...props
} ) => {
	const {
		id,
		label,
		help,
		className,
		defaultValue: simpleDefault,
		options,
		value: simpleValue,
		getOptionValue = defaultGetOptionValue,
		isMulti,
	} = props;
	const value = getValue( options, simpleValue, getOptionValue, isMulti );
	const defaultValue = getValue(
		options,
		simpleDefault,
		getOptionValue,
		isMulti
	);
	const select = cloneElement( children, {
		styles: {
			...wpStyles,
			...styles,
		},
		components: {
			...wpComponents,
			...components,
		},
		theme: ( baseTheme ) => ( {
			...baseTheme,
			colors: {
				...baseTheme.colors,
				text: '#3c434a',
				primary25: 'var(--wp-admin-theme-color)',
				primary: 'var(--wp-admin-theme-color)',
			},
		} ),
		classNamePrefix: 'eaccounting_select',
		isDisabled,
		isInvalid,
		...props,
		defaultValue,
		getOptionValue,
		isMulti,
		options,
		value,
	} );

	return (
		<BaseControl
			id={ id }
			label={ label }
			help={ help }
			className={ className }
		>
			{ select }
		</BaseControl>
	);
};

const Select = forwardRef( ( props, ref ) => {
	const { isCreatable, isAsync } = props;
	switch ( true ) {
		case isCreatable:
			return (
				<SelectControl { ...props }>
					<CreatableReactSelect ref={ ref } />
				</SelectControl>
			);
		case isAsync:
			return (
				<SelectControl { ...props }>
					<AsyncReactSelect ref={ ref } />
				</SelectControl>
			);
		default:
			return (
				<SelectControl { ...props }>
					<ReactSelect ref={ ref } />
				</SelectControl>
			);
	}
} );

Select.ActionMeta = ActionMeta;
Select.OnChangeValue = OnChangeValue;

export default Select;
