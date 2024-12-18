/* eslint-disable no-underscore-dangle */
import React, { useCallback, useMemo, useRef, useState } from 'react';
import { useCombobox, useMultipleSelection } from 'downshift';
import useRenderers from 'hooks/useRenderers';
import { ensureIsArray, isPresent } from 'lib/utils';
import { chakra, useMultiStyleConfig } from '@chakra-ui/react';
import Menu, { MenuCreateItem, MenuItem } from './components/Menu';
import SelectedItems from './components/SelectedItems';
import Input from './components/Input';
import Indicators from './components/Indicators';
import DropdownIndicator from './components/DropdownIndicator';
import ClearIndicator from './components/ClearIndicator';
import Container from './components/Container';
import SelectedItem from './components/SelectedItem';
import Label from './components/Label';
import LoadingIndicator from './components/LoadingIndicator';

export default function Select( {
	choices: choices_,
	itemToString: itemToString_,
	filterItems: filterItems_,
	stringToItem: stringToItem_,
	renderers,
	id,
	name,
	label,
	placeholder = `Select ${ name }`,
	size = 'sm',
	variant = 'outline',
	creatable = false,
	trimInput = true,
	closeOnSelect = true,
	clearSearchOnSelect = true,
	readOnly = false,
	onInputChange,
	getItemValue: getItemValue_,
	width,
	minWidth,
	maxWidth,
	menuWidth,
	menuMinWidth,
	menuMaxWidth,
	initialIsOpen,
	...restProps
} ) {
	const { isMulti } = restProps;
	const mode = isMulti ? restProps.mode ?? 'inside' : 'keep';
	const keepSelected = isMulti ? ( restProps.keepSelected ?? mode === 'keep' ) || false : true;

	// Item <> value conversion functions
	const itemToString = useMemo(
		() =>
			itemToString_ ||
			( ( item ) =>
				typeof item === 'string'
					? item
					: item && 'id' in item
					? item.id || ''
					: item && 'label' in item
					? item.label || ''
					: '' ),
		[ itemToString_ ]
	);

	const stringToItem = useMemo(
		() =>
			stringToItem_ ||
			( ( text ) => ( {
				label: text,
				value: text,
			} ) ),
		[ stringToItem_ ]
	);

	const filterItems = useMemo(
		() =>
			filterItems_ ||
			( ( items, search ) =>
				items.filter( ( x ) =>
					itemToString( x ).toLowerCase().includes( search.toLowerCase() )
				) ),
		[ filterItems_, itemToString ]
	);

	const getItemValue = useMemo(
		() =>
			getItemValue_ ||
			( ( item ) =>
				typeof item === 'object' && item && 'value' in item ? item.value : item ),
		[ getItemValue_ ]
	);

	const value = useMemo( () => ensureIsArray( restProps.value ), [ restProps.value ] );
	const valueItems = useMemo(
		() => value.map( ( x ) => ( typeof x === 'string' ? stringToItem( x ) : x ) ),
		[ stringToItem, value ]
	);
	const [ inputValue, setInputValue ] = useState( '' );
	const trimmedInputValue = trimInput ? inputValue.trim() : inputValue;
	const styles = useMultiStyleConfig( 'FauxSelect', { size, variant, mode } );
	const isAsync = typeof choices_ === 'function';
	const [ isFocused, setIsFocused ] = useState( false );
	const [ isHovering, setIsHovering ] = useState( false );
	const [ loadedChoices, setLoadedChoices ] = useState( isAsync ? undefined : choices_ );
	const [ isLoading, setIsLoading ] = useState();

	const [ choices, choicesMap ] = useMemo( () => {
		const items = loadedChoices || valueItems;
		const itemsMap = new Map();
		items.forEach( ( choice ) => {
			itemsMap.set( getItemValue( choice ), choice );
		} );
		return [ items, itemsMap ];
	}, [ getItemValue, loadedChoices, valueItems ] );

	// Convert value to known choices. Keep invalid values so that renderer
	// can decide how to render them (e.g. render as a placeholder item).
	const selectedChoices = useMemo(
		() => value.map( ( val ) => choicesMap.get( val ) || val ),
		[ value, choicesMap ]
	);

	const isItemSelected = useCallback(
		( item ) => {
			return keepSelected && selectedChoices.includes( item );
		},
		[ keepSelected, selectedChoices ]
	);

	const inputRef = useRef( null );
	const changeTrigger = useRef();
	const needBlur = useRef( false );

	const changeInputValue = useCallback(
		( inputVal ) => {
			setInputValue( inputVal || '' );
			if ( onInputChange ) {
				onInputChange( inputVal );
			}
		},
		[ onInputChange ]
	);

	const {
		getDropdownProps,
		reset: resetSelection,
		addSelectedItem,
		removeSelectedItem,
		selectedItems,
	} = useMultipleSelection( {
		itemToString,
		selectedItems: selectedChoices.filter( isPresent ),
		onSelectedItemsChange: ( { selectedItems: newSelectedItems } ) => {
			if ( restProps.onChange ) {
				const itemValues = newSelectedItems?.map( ( x ) => getItemValue( x ) ) || [];
				if ( isMulti ) {
					restProps.onChange( itemValues, changeTrigger.current );
				} else {
					restProps.onChange(
						itemValues[ itemValues.length - 1 ],
						changeTrigger.current
					);
				}
			}
		},
	} );

	const selectedItemsIds = useMemo(
		() => new Set( selectedItems.map( itemToString ) ),
		[ itemToString, selectedItems ]
	);

	const [ createdIndex, filteredItems ] = useMemo( () => {
		let createdOptionIndex = -1;

		const filteredOptions = filterItems(
			keepSelected
				? choices
				: choices.filter( ( item ) => ! selectedItemsIds.has( itemToString( item ) ) ),
			trimmedInputValue
		);

		if (
			creatable &&
			trimmedInputValue &&
			! choices
				.concat( selectedItems )
				.find( ( x ) => itemToString( x ) === trimmedInputValue )
		) {
			createdOptionIndex = filteredOptions.length;
			filteredOptions.push( stringToItem( trimmedInputValue ) );
		}
		return [ createdOptionIndex, filteredOptions ];
	}, [
		filterItems,
		keepSelected,
		choices,
		trimmedInputValue,
		creatable,
		selectedItems,
		selectedItemsIds,
		itemToString,
		stringToItem,
	] );

	const {
		isOpen,
		getMenuProps,
		getInputProps,
		getComboboxProps,
		highlightedIndex,
		getItemProps,
		openMenu,
		closeMenu,
		toggleMenu,
		reset: resetCombobox,
	} = useCombobox( {
		id,
		inputValue,
		initialIsOpen,
		defaultHighlightedIndex: isFocused ? 0 : -1, // after selection, highlight the first item.
		items: filteredItems,
		itemToString,
		stateReducer: ( state, { changes, type, index } ) => {
			// Click item or press Enter
			if (
				type === useCombobox.stateChangeTypes.ItemClick ||
				type === useCombobox.stateChangeTypes.InputKeyDownEnter
			) {
				const selected = filteredItems[ state.highlightedIndex ];
				return {
					...changes,
					selectedItem: selected,
					// highlight previous item when current item is added to the selected list
					highlightedIndex: keepSelected
						? highlightedIndex
						: Math.max( 0, Math.min( ( index || 0 ) - 1, filteredItems.length - 2 ) ),
					inputValue: clearSearchOnSelect ? '' : state.inputValue,
					isOpen: ! closeOnSelect,
				};
			}
			return changes;
		},
		onStateChange: ( { inputValue: inputVal, type, selectedItem } ) => {
			needBlur.current = false;
			if ( type === useCombobox.stateChangeTypes.InputChange ) {
				changeInputValue( inputVal || '' );
				return;
			}
			if ( type === useCombobox.stateChangeTypes.InputBlur ) {
				setIsFocused( false );
			}
			if (
				( type === useCombobox.stateChangeTypes.InputKeyDownEnter ||
					type === useCombobox.stateChangeTypes.ItemClick ||
					// except in `keep` mode, bluring input will also add the item
					( type === useCombobox.stateChangeTypes.InputBlur && mode !== 'keep' ) ) &&
				selectedItem
			) {
				if ( ! isMulti ) {
					inputRef.current?.blur?.();
				}
				changeTrigger.current = type;

				if ( selectedItems.includes( selectedItem ) ) {
					removeSelectedItem( selectedItem );
				} else {
					addSelectedItem(
						createdIndex !== -1 &&
							filteredItems[ createdIndex ] === selectedItem &&
							'value' in selectedItem &&
							'label' in selectedItem
							? selectedItem.value
							: selectedItem
					);
				}
				if ( clearSearchOnSelect ) {
					changeInputValue( '' );
				}
				if ( mode === 'keep' ) {
					// reset `selectedItem` so that we can click on the same item twice
					// to toggle selection.
					resetCombobox();
				}
			}
		},
	} );

	const loadChoices = useCallback(
		( input ) => {
			if ( isAsync ) {
				const result = choices_( input );
				if ( result instanceof Promise ) {
					setIsLoading( true );
					result
						.then( ( items ) => {
							setLoadedChoices( items );
						} )
						.finally( () => {
							setIsLoading( false );
						} );
				}
			}
		},
		[ choices_, isAsync ]
	);

	const dropdownProps = useMemo(
		() =>
			getDropdownProps( {
				preventKeyAction: isOpen,
			} ),
		[ getDropdownProps, isOpen ]
	);

	const inputProps = useMemo( () => {
		let valueText = inputValue;
		if ( mode === 'keep' && ( selectedItems.length > 0 || valueText ) ) {
			valueText = isMulti
				? `${ selectedItems.length } selected`
				: valueText || itemToString( selectedItems[ 0 ] );
		}
		return getInputProps( {
			id,
			name,
			placeholder: valueText || placeholder,
			readOnly,
			value: isFocused ? inputValue : valueText,
			ref: ( el ) => {
				inputRef.current = el;
				dropdownProps.ref( el );
			},
			onClick: () => {
				if ( isFocused && ! isOpen ) {
					openMenu();
				}
			},
			onFocus: () => {
				if ( isAsync && ! loadedChoices ) {
					loadChoices( inputValue );
				}
				if ( ! isOpen ) {
					openMenu();
				}
				if ( ! isFocused ) {
					setIsFocused( true );
				}
				needBlur.current = false;
			},
			onBlur: () => {
				needBlur.current = true;
				setTimeout( () => {
					if ( needBlur.current ) {
						setIsFocused( false );
					}
				}, 50 );
			},
			onKeyDown: ( e ) => {
				if ( e.key === 'Escape' ) {
					e.stopPropagation();
				} else if (
					e.key === 'Backspace' &&
					selectedItems.length > 0 &&
					! e.currentTarget.value
				) {
					// the default `onKeyDown` event will delete the item already
					changeTrigger.current = 'backspace-delete';
				}
				if ( dropdownProps.onKeyDown ) {
					dropdownProps.onKeyDown( e );
				}
			},
		} );
	}, [
		dropdownProps,
		getInputProps,
		id,
		inputValue,
		isAsync,
		isFocused,
		isMulti,
		isOpen,
		itemToString,
		loadChoices,
		loadedChoices,
		mode,
		name,
		openMenu,
		placeholder,
		readOnly,
		selectedItems,
	] );

	const sharedProps = useMemo(
		() => ( {
			variant,
			size,
			mode,
			styles,
			isAsync,
			isFocused,
			isHovering,
			isLoading,
			isMulti,
			isOpen,
			itemToString,
			setIsHovering,
			setIsFocused,
			openMenu,
			closeMenu,
			toggleMenu,
			readOnly,
			removeSelectedItem,
			addSelectedItem,
			resetSelection: () => {
				changeInputValue( '' );
				if ( selectedItems.length > 0 ) {
					resetSelection();
					// must also reset combobox's `selectedItem`, otherwise
					// you won't be able to re-select cleared values.
					resetCombobox();
				}
			},
			isItemSelected,
			choices,
			selectedItems,
			filteredItems,
			highlightedIndex,
			createdIndex,
			inputRef,
			needBlur,
		} ),
		[
			variant,
			size,
			mode,
			styles,
			isAsync,
			isFocused,
			isHovering,
			isLoading,
			isMulti,
			isOpen,
			itemToString,
			openMenu,
			closeMenu,
			toggleMenu,
			readOnly,
			removeSelectedItem,
			addSelectedItem,
			isItemSelected,
			choices,
			selectedItems,
			filteredItems,
			highlightedIndex,
			createdIndex,
			changeInputValue,
			resetSelection,
			resetCombobox,
		]
	);

	const render = useRenderers(
		{
			input: Input,
			container: Container,
			menu: Menu,
			menuItem: MenuItem,
			menuCreateItem: MenuCreateItem,
			indicators: Indicators,
			dropdownIndicator: DropdownIndicator,
			clearIndicator: ClearIndicator,
			loadingIndicator: LoadingIndicator,
			selectedItem: SelectedItem,
			selectedItems: SelectedItems,
			label: Label,
			...renderers,
		},
		sharedProps
	);

	return (
		<>
			{ render( 'label', { id, name, label, inputValue } ) }
			<chakra.div width={ width } minWidth={ minWidth } maxWidth={ maxWidth }>
				<div { ...getComboboxProps() }>
					{ render( 'container', {
						inputProps,
						onClick: dropdownProps.onClick,
					} ) }
				</div>
				{ render( 'menu', {
					getMenuProps,
					getItemProps,
					menuWidth,
					menuMinWidth,
					menuMaxWidth,
				} ) }
			</chakra.div>
		</>
	);
}

export function SingleSelect( props ) {
	return <Select { ...props } isMulti={ false } />;
}

export function MultiSelect( props ) {
	return <Select { ...props } isMulti />;
}
