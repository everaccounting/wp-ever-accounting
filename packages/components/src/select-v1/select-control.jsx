/**
 * External dependencies
 */
import classnames from 'classnames';
import { useCombobox, useMultipleSelection } from 'downshift';
/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { useState, useEffect, useRef } from '@wordpress/element';
import { chevronDown } from '@wordpress/icons';
import { BaseControl } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { SelectedItems } from './selected-items';
import { ComboBox } from './combo-box';
import { Menu } from './menu';
import { MenuItem } from './menu-item';
import { SuffixIcon } from './suffix-icon';
import { defaultGetItemLabel, defaultGetItemValue, defaultGetFilteredItems } from './utils';
export const selectControlStateChangeTypes = useCombobox.stateChangeTypes;
function SelectControl( {
	getOptionLabel = defaultGetItemLabel,
	getOptionValue = defaultGetItemValue,
	hasExternalTags = false,
	multiple = false,
	items,
	label,
	getFilteredItems = defaultGetFilteredItems,
	onInputChange = () => null,
	onRemove = () => null,
	onSelect = () => null,
	onFocus = () => null,
	onKeyDown = () => null,
	stateReducer = ( state, actionAndChanges ) => actionAndChanges.changes,
	placeholder,
	selected,
	className,
	disabled,
	inputProps = {},
	suffix = <SuffixIcon icon={ chevronDown } />,
	showToggleButton = false,
	readOnlyWhenClosed = false,
	__experimentalOpenMenuOnFocus = false,
} ) {
	const [ isFocused, setIsFocused ] = useState( false );
	const [ inputValue, setInputValue ] = useState( '' );
	const instanceId = useInstanceId( SelectControl, 'eac-select-control' );
	const innerInputClassName = 'eac-select-control__input';
	const selectControlWrapperRef = useRef( null );
	let selectedItems = selected === null ? [] : selected;
	selectedItems = Array.isArray( selectedItems )
		? selectedItems
		: [ selectedItems ].filter( Boolean );
	const singleSelectedItem = ! multiple && selectedItems.length ? selectedItems[ 0 ] : null;
	const filteredItems = getFilteredItems( items, inputValue, selectedItems, getOptionLabel );
	const { getSelectedItemProps, getDropdownProps, removeSelectedItem } = useMultipleSelection( {
		itemToString: getOptionLabel,
		selectedItems,
	} );
	useEffect( () => {
		if ( multiple ) {
			return;
		}
		setInputValue( getOptionLabel( singleSelectedItem ) );
	}, [ getOptionLabel, multiple, singleSelectedItem ] );

	const {
		isOpen,
		getLabelProps,
		getMenuProps,
		getToggleButtonProps,
		getInputProps,
		highlightedIndex,
		getItemProps,
		selectItem,
		selectedItem: comboboxSingleSelectedItem,
		openMenu,
		closeMenu,
		getComboboxProps,
	} = useCombobox( {
		id: instanceId,
		initialSelectedItem: singleSelectedItem,
		inputValue,
		items: filteredItems,
		selectedItem: multiple ? null : singleSelectedItem,
		itemToString: getOptionLabel,
		onSelectedItemChange: ( { selectedItem } ) => {
			if ( selectedItem ) {
				onSelect( selectedItem );
			} else if ( singleSelectedItem ) {
				onRemove( singleSelectedItem );
			}
		},
		onInputValueChange: ( { inputValue: value, ...changes } ) => {
			if ( value !== undefined ) {
				setInputValue( value );
				onInputChange( value, changes );
			}
		},
		stateReducer: ( state, actionAndChanges ) => {
			const { changes, type } = actionAndChanges;
			let newChanges;
			switch ( type ) {
				case selectControlStateChangeTypes.InputBlur:
					// Set input back to selected item if there is a selected item, blank otherwise.
					newChanges = {
						...changes,
						selectedItem:
							! changes.inputValue?.length && ! multiple
								? null
								: changes.selectedItem,
						inputValue:
							changes.selectedItem === state.selectedItem &&
							changes.inputValue?.length &&
							! multiple
								? getOptionLabel( comboboxSingleSelectedItem )
								: '',
					};
					break;
				case selectControlStateChangeTypes.InputKeyDownEnter:
				case selectControlStateChangeTypes.FunctionSelectItem:
				case selectControlStateChangeTypes.ItemClick:
					if ( changes.selectedItem && multiple ) {
						newChanges = {
							...changes,
							inputValue: '',
						};
					}
					break;
				default:
					break;
			}
			return stateReducer( state, {
				...actionAndChanges,
				changes: newChanges ?? changes,
			} );
		},
	} );
	return (
		<BaseControl
			id={ instanceId }
			{ ...getLabelProps() }
			label={ label }
			className={ classnames( 'eac-select-control', className, {
				'is-focused': isFocused,
				'is-multiple': multiple,
				'has-selected-items': selectedItems.length,
			} ) }
		>
			<div ref={ getComboboxProps().ref }>
				<input { ...getInputProps( getDropdownProps( { preventKeyAction: isOpen } ) ) } />
				<button type="button" { ...getToggleButtonProps() } aria-label="toggle menu">
					&#8595;
				</button>
			</div>

			{ isOpen && (
				<ul { ...getMenuProps() }>
					{ filteredItems.map( ( item, index ) => (
						<li
							key={ `${ getOptionValue( item ) }${ index }` }
							{ ...getItemProps( { item, index } ) }
							className="eac-select-control__menu-item"
						>
							{ getOptionLabel( item ) }
						</li>
					) ) }
				</ul>
			) }
		</BaseControl>
	);
}

export { SelectControl };
