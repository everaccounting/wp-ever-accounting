/**
 * External dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { isNull } from 'lodash';
/**
 * WordPress dependencies
 */
import { useRef, useState, useCallback, useEffect } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { useControlledState, usePreviousProps, useEventCallback } from '../../utils';
const MULTIPLE_DEFAULT_VALUE = [];
const pageSize = 5;
// https://stackoverflow.com/questions/990904/remove-accents-diacritics-in-a-string-in-javascript
function stripDiacritics( string ) {
	return string.normalize( 'NFD' ).replace( /[\u0300-\u036f]/g, '' );
}
function createFilterOptions( config = {} ) {
	const {
		ignoreAccents = true,
		ignoreCase = true,
		limit,
		matchFrom = 'any',
		stringify,
		trim = false,
	} = config;

	return ( options, { inputValue, getOptionLabel } ) => {
		let input = trim ? inputValue.trim() : inputValue;
		if ( ignoreCase ) {
			input = input.toLowerCase();
		}
		if ( ignoreAccents ) {
			input = stripDiacritics( input );
		}

		const filteredOptions = ! input
			? options
			: options.filter( ( option ) => {
					let candidate = stringify || getOptionLabel( option );
					if ( ignoreCase ) {
						candidate = candidate.toLowerCase();
					}
					if ( ignoreAccents ) {
						candidate = stripDiacritics( candidate );
					}

					return matchFrom === 'start'
						? candidate.startsWith( input )
						: candidate.includes( input );
			  } );

		return typeof limit === 'number' ? filteredOptions.slice( 0, limit ) : filteredOptions;
	};
}

const defaultIsActiveElementInListbox = ( listboxRef ) =>
	listboxRef.current !== null &&
	listboxRef.current.parentElement?.contains( document.activeElement );

export function useAutocomplete( props = {} ) {
	const {
		unstable_isActiveElementInListBox = defaultIsActiveElementInListbox,
		autoComplete = false,
		autoHighlight = false,
		autoSelect = false,
		blurOnSelect = false,
		clearOnBlur = ! props.freeSolo,
		clearOnEscape = false,
		defaultValue = props.multiple ? MULTIPLE_DEFAULT_VALUE : null,
		disableClearable = false,
		disableCloseOnSelect = false,
		disabled: disabledProp,
		disabledItemsFocusable = false,
		disableListWrap = false,
		filterOptions = createFilterOptions(),
		filterSelectedOptions = false,
		getOptionDisabled,
		getOptionKey,
		getOptionLabel = ( option ) => option.label ?? option,
		handleHomeEndKeys = false,
		id: idProp,
		includeInputInList = false,
		inputValue: inputValueProp,
		isOptionEqualToValue = ( option, value ) => option === value,
		multiple = false,
		onChange,
		onClose,
		onHighlightChange,
		onInputChange,
		onOpen,
		open: openProp,
		openOnFocus = false,
		options = [],
		readOnly = false,
		selectOnFocus = ! props.freeSolo,
		value: valueProp,
	} = props;

	const id = useInstanceId( useAutocomplete, 'eac-select-control', idProp );
	const isAsync = typeof options === 'function';

	const ignoreFocus = useRef( false );
	const firstFocus = useRef( true );
	const inputRef = useRef( null );
	const listboxRef = useRef( null );
	const defaultHighlighted = autoHighlight ? 0 : -1;
	const highlightedIndexRef = useRef( defaultHighlighted );
	const [ focused, setFocused ] = useState( false );
	const [ isLoading, setIsLoading ] = useState( false );

	const [ anchorEl, setAnchorEl ] = useState( null );
	const [ focusedTag, setFocusedTag ] = useState( -1 );
	const [ inputPristine, setInputPristine ] = useState( true );
	const [ value, setValueState ] = useControlledState( {
		current: valueProp,
		default: defaultValue,
	} );
	const [ open, setOpenState ] = useControlledState( {
		current: openProp,
		default: false,
	} );
	const [ inputValue, setInputValueState ] = useControlledState( {
		current: inputValueProp,
		default: '',
	} );

	// Computed values
	const inputValueIsSelectedValue =
		! multiple && ! isNull( value ) && inputValue === getOptionLabel( value );
	const popupOpen = open && ! readOnly;

	const filteredOptions = popupOpen
		? filterOptions(
				options.filter( ( option ) => {
					return ! (
						filterSelectedOptions &&
						( multiple ? value : [ value ] ).some(
							( value2 ) => value2 !== null && isOptionEqualToValue( option, value2 )
						)
					);
				} ), // we use the empty string to manipulate `filterOptions` to not filter any options
				// i.e. the filter predicate always returns true
				{
					inputValue: inputValueIsSelectedValue && inputPristine ? '' : inputValue,
					getOptionLabel,
				}
		  )
		: [];

	const previousProps = usePreviousProps( {
		filteredOptions,
		value,
		inputValue,
	} );

	const resetInputValue = useCallback(
		( event, newValue, reason ) => {
			// retain current `inputValue` if new option isn't selected and `clearOnBlur` is false
			// When `multiple` is enabled, `newValue` is an array of all selected items including the newly selected item
			const isOptionSelected = multiple ? value.length < newValue.length : newValue !== null;
			if ( ! isOptionSelected && ! clearOnBlur ) {
				return;
			}
			let newInputValue;
			if ( multiple ) {
				newInputValue = '';
			} else if ( newValue === null ) {
				newInputValue = '';
			} else {
				const optionLabel = getOptionLabel( newValue );
				newInputValue = typeof optionLabel === 'string' ? optionLabel : '';
			}

			if ( inputValue === newInputValue ) {
				return;
			}

			setInputValueState( newInputValue );

			if ( onInputChange ) {
				onInputChange( event, newInputValue, reason );
			}
		},
		[
			getOptionLabel,
			inputValue,
			multiple,
			onInputChange,
			setInputValueState,
			clearOnBlur,
			value,
		]
	);

	useEffect( () => {
		const valueChange = value !== previousProps.value;

		if ( focused && ! valueChange ) {
			return;
		}

		resetInputValue( null, value, 'reset' );
	}, [ value, resetInputValue, focused, previousProps.value ] );

	const listboxAvailable = open && filteredOptions.length > 0 && ! readOnly;

	const focusTag = useEventCallback( ( tagToFocus ) => {
		if ( tagToFocus === -1 ) {
			inputRef.current.focus();
		} else {
			anchorEl.querySelector( `[data-tag-index="${ tagToFocus }"]` ).focus();
		}
	} );
	// Ensure the focusedTag is never inconsistent
	useEffect( () => {
		if ( multiple && focusedTag > value.length - 1 ) {
			setFocusedTag( -1 );
			focusTag( -1 );
		}
	}, [ value, multiple, focusedTag, focusTag ] );

	function validOptionIndex( index, direction ) {
		if ( ! listboxRef.current || index < 0 || index >= filteredOptions.length ) {
			return -1;
		}

		let nextFocus = index;

		while ( true ) {
			const option = listboxRef.current.querySelector(
				`[data-option-index="${ nextFocus }"]`
			);

			// Same logic as MenuList.js
			const nextFocusDisabled = disabledItemsFocusable
				? false
				: ! option || option.disabled || option.getAttribute( 'aria-disabled' ) === 'true';

			if ( option && option.hasAttribute( 'tabindex' ) && ! nextFocusDisabled ) {
				// The next option is available
				return nextFocus;
			}

			// The next option is disabled, move to the next element.
			// with looped index
			if ( direction === 'next' ) {
				nextFocus = ( nextFocus + 1 ) % filteredOptions.length;
			} else {
				nextFocus = ( nextFocus - 1 + filteredOptions.length ) % filteredOptions.length;
			}

			// We end up with initial index, that means we don't have available options.
			// All of them are disabled
			if ( nextFocus === index ) {
				return -1;
			}
		}
	}

	const setHighlightedIndex = useEventCallback( ( { event, index, reason = 'auto' } ) => {
		highlightedIndexRef.current = index;

		// does the index exist?
		if ( index === -1 ) {
			inputRef.current.removeAttribute( 'aria-activedescendant' );
		} else {
			inputRef.current.setAttribute( 'aria-activedescendant', `${ id }-option-${ index }` );
		}

		if ( onHighlightChange ) {
			onHighlightChange( event, index === -1 ? null : filteredOptions[ index ], reason );
		}

		if ( ! listboxRef.current ) {
			return;
		}

		const prev = listboxRef.current.querySelector( `[role="option"].eac-focused` );
		if ( prev ) {
			prev.classList.remove( `eac-focused` );
			prev.classList.remove( `eac-focusVisible` );
		}

		let listboxNode = listboxRef.current;
		if ( listboxRef.current.getAttribute( 'role' ) !== 'listbox' ) {
			listboxNode = listboxRef.current.parentElement.querySelector( '[role="listbox"]' );
		}

		// "No results"
		if ( ! listboxNode ) {
			return;
		}

		if ( index === -1 ) {
			listboxNode.scrollTop = 0;
			return;
		}

		const option = listboxRef.current.querySelector( `[data-option-index="${ index }"]` );

		if ( ! option ) {
			return;
		}

		option.classList.add( `eac-focused` );
		if ( reason === 'keyboard' ) {
			option.classList.add( `eac-focusVisible` );
		}

		// Scroll active descendant into view.
		// Logic copied from https://www.w3.org/WAI/content-assets/wai-aria-practices/patterns/combobox/examples/js/select-only.js
		// In case of mouse clicks and touch (in mobile devices) we avoid scrolling the element and keep both behaviors same.
		// Consider this API instead once it has a better browser support:
		// .scrollIntoView({ scrollMode: 'if-needed', block: 'nearest' });
		if (
			listboxNode.scrollHeight > listboxNode.clientHeight &&
			reason !== 'mouse' &&
			reason !== 'touch'
		) {
			const element = option;

			const scrollBottom = listboxNode.clientHeight + listboxNode.scrollTop;
			const elementBottom = element.offsetTop + element.offsetHeight;
			if ( elementBottom > scrollBottom ) {
				listboxNode.scrollTop = elementBottom - listboxNode.clientHeight;
			} else if ( element.offsetTop - element.offsetHeight < listboxNode.scrollTop ) {
				listboxNode.scrollTop = element.offsetTop - element.offsetHeight;
			}
		}
	} );

	const changeHighlightedIndex = useEventCallback(
		( { event, diff, direction = 'next', reason = 'auto' } ) => {
			if ( ! popupOpen ) {
				return;
			}

			const getNextIndex = () => {
				const maxIndex = filteredOptions.length - 1;

				if ( diff === 'reset' ) {
					return defaultHighlighted;
				}

				if ( diff === 'start' ) {
					return 0;
				}

				if ( diff === 'end' ) {
					return maxIndex;
				}

				const newIndex = highlightedIndexRef.current + diff;

				if ( newIndex < 0 ) {
					if ( newIndex === -1 && includeInputInList ) {
						return -1;
					}

					if (
						( disableListWrap && highlightedIndexRef.current !== -1 ) ||
						Math.abs( diff ) > 1
					) {
						return 0;
					}

					return maxIndex;
				}

				if ( newIndex > maxIndex ) {
					if ( newIndex === maxIndex + 1 && includeInputInList ) {
						return -1;
					}

					if ( disableListWrap || Math.abs( diff ) > 1 ) {
						return maxIndex;
					}

					return 0;
				}

				return newIndex;
			};

			const nextIndex = validOptionIndex( getNextIndex(), direction );
			setHighlightedIndex( { index: nextIndex, reason, event } );

			// Sync the content of the input with the highlighted option.
			if ( autoComplete && diff !== 'reset' ) {
				if ( nextIndex === -1 ) {
					inputRef.current.value = inputValue;
				} else {
					const option = getOptionLabel( filteredOptions[ nextIndex ] );
					inputRef.current.value = option;

					// The portion of the selected suggestion that has not been typed by the user,
					// a completion string, appears inline after the input cursor in the textbox.
					const index = option.toLowerCase().indexOf( inputValue.toLowerCase() );
					if ( index === 0 && inputValue.length > 0 ) {
						inputRef.current.setSelectionRange( inputValue.length, option.length );
					}
				}
			}
		}
	);

	const getPreviousHighlightedOptionIndex = () => {
		const isSameValue = ( value1, value2 ) => {
			const label1 = value1 ? getOptionLabel( value1 ) : '';
			const label2 = value2 ? getOptionLabel( value2 ) : '';
			return label1 === label2;
		};

		if (
			highlightedIndexRef.current !== -1 &&
			previousProps.filteredOptions &&
			previousProps.filteredOptions.length !== filteredOptions.length &&
			previousProps.inputValue === inputValue &&
			( multiple
				? value.length === previousProps.value.length &&
				  previousProps.value.every(
						( val, i ) => getOptionLabel( value[ i ] ) === getOptionLabel( val )
				  )
				: isSameValue( previousProps.value, value ) )
		) {
			const previousHighlightedOption =
				previousProps.filteredOptions[ highlightedIndexRef.current ];

			if ( previousHighlightedOption ) {
				return filteredOptions.findIndex( ( option ) => {
					return getOptionLabel( option ) === getOptionLabel( previousHighlightedOption );
				} );
			}
		}
		return -1;
	};

	const syncHighlightedIndex = useCallback( () => {
		if ( ! popupOpen ) {
			return;
		}

		// Check if the previously highlighted option still exists in the updated filtered options list and if the value and inputValue haven't changed
		// If it exists and the value and the inputValue haven't changed, just update its index, otherwise continue execution
		const previousHighlightedOptionIndex = getPreviousHighlightedOptionIndex();
		if ( previousHighlightedOptionIndex !== -1 ) {
			highlightedIndexRef.current = previousHighlightedOptionIndex;
			return;
		}

		const valueItem = multiple ? value[ 0 ] : value;

		// The popup is empty, reset
		if ( filteredOptions.length === 0 || valueItem === null ) {
			changeHighlightedIndex( { diff: 'reset' } );
			return;
		}

		if ( ! listboxRef.current ) {
			return;
		}

		// Synchronize the value with the highlighted index
		if ( valueItem !== null ) {
			const currentOption = filteredOptions[ highlightedIndexRef.current ];

			// Keep the current highlighted index if possible
			if (
				multiple &&
				currentOption &&
				value.findIndex( ( val ) => isOptionEqualToValue( currentOption, val ) ) !== -1
			) {
				return;
			}

			const itemIndex = filteredOptions.findIndex( ( optionItem ) =>
				isOptionEqualToValue( optionItem, valueItem )
			);
			if ( itemIndex === -1 ) {
				changeHighlightedIndex( { diff: 'reset' } );
			} else {
				setHighlightedIndex( { index: itemIndex } );
			}
			return;
		}

		// Prevent the highlighted index to leak outside the boundaries.
		if ( highlightedIndexRef.current >= filteredOptions.length - 1 ) {
			setHighlightedIndex( { index: filteredOptions.length - 1 } );
			return;
		}

		// Restore the focus to the previous index.
		setHighlightedIndex( { index: highlightedIndexRef.current } );
		// Ignore filteredOptions (and options, isOptionEqualToValue, getOptionLabel) not to break the scroll position
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [
		// Only sync the highlighted index when the option switch between empty and not
		filteredOptions.length,
		// Don't sync the highlighted index with the value when multiple
		// eslint-disable-next-line react-hooks/exhaustive-deps
		multiple ? false : value,
		filterSelectedOptions,
		changeHighlightedIndex,
		setHighlightedIndex,
		popupOpen,
		inputValue,
		multiple,
	] );

	const handleListBoxRef = useEventCallback( ( node ) => {
		if ( typeof listboxRef === 'function' ) {
			listboxRef( node );
		} else if ( listboxRef ) {
			listboxRef.current = node;
		}

		if ( ! node ) {
			return;
		}

		syncHighlightedIndex();
	} );

	useEffect( () => {
		syncHighlightedIndex();
	}, [ syncHighlightedIndex ] );

	const loadOptions = useCallback(
		( inputValue ) => {
			if ( ! isAsync ) {
				return;
			}

			setIsLoading( true );

			const promise = options( inputValue );

			if ( ! promise || ! promise.then ) {
				console.error( 'The `options` function must return a promise.' );
				setIsLoading( false );
				return;
			}

			promise.then( ( result ) => {
				if ( ! Array.isArray( result ) ) {
					console.error( 'The promise must resolve to an array.' );
					setIsLoading( false );
					return;
				}

				setIsLoading( false );

				if ( ! open ) {
					return;
				}

				const filteredOptions = filterOptions( result, {
					inputValue: inputValueIsSelectedValue && inputPristine ? '' : inputValue,
					getOptionLabel,
				} );

				if ( filteredOptions.length === 0 ) {
					return;
				}

				if ( filteredOptions.length > 0 ) {
					setHighlightedIndex( { index: defaultHighlighted, reason: 'auto' } );
				}
			} );
		},
		[
			defaultHighlighted,
			filterOptions,
			getOptionLabel,
			inputPristine,
			inputValue,
			inputValueIsSelectedValue,
			isAsync,
			open,
			options,
			setHighlightedIndex,
		]
	);

	const handleOpen = ( event ) => {
		if ( open ) {
			return;
		}

		setOpenState( true );
		setInputPristine( true );

		if ( onOpen ) {
			onOpen( event );
		}
	};

	const handleClose = ( event, reason ) => {
		if ( ! open ) {
			return;
		}

		setOpenState( false );

		if ( onClose ) {
			onClose( event, reason );
		}
	};

	const handleValue = ( event, newValue, reason, details ) => {
		if ( multiple ) {
			if (
				value.length === newValue.length &&
				value.every( ( val, i ) => val === newValue[ i ] )
			) {
				return;
			}
		} else if ( value === newValue ) {
			return;
		}

		if ( onChange ) {
			onChange( event, newValue, reason, details );
		}

		setValueState( newValue );
	};

	const isTouch = useRef( false );

	const selectNewValue = ( event, option, reasonProp = 'selectOption', origin = 'options' ) => {
		let reason = reasonProp;
		let newValue = option;

		if ( multiple ) {
			newValue = Array.isArray( value ) ? value.slice() : [];

			const itemIndex = newValue.findIndex( ( valueItem ) =>
				isOptionEqualToValue( option, valueItem )
			);

			if ( itemIndex === -1 ) {
				newValue.push( option );
			} else if ( origin !== 'freeSolo' ) {
				newValue.splice( itemIndex, 1 );
				reason = 'removeOption';
			}
		}

		resetInputValue( event, newValue, reason );

		handleValue( event, newValue, reason, { option } );
		if ( ! disableCloseOnSelect && ( ! event || ( ! event.ctrlKey && ! event.metaKey ) ) ) {
			handleClose( event, reason );
		}

		if (
			blurOnSelect === true ||
			( blurOnSelect === 'touch' && isTouch.current ) ||
			( blurOnSelect === 'mouse' && ! isTouch.current )
		) {
			inputRef.current.blur();
		}
	};

	function validTagIndex( index, direction ) {
		if ( index === -1 ) {
			return -1;
		}

		let nextFocus = index;

		while ( true ) {
			// Out of range
			if (
				( direction === 'next' && nextFocus === value.length ) ||
				( direction === 'previous' && nextFocus === -1 )
			) {
				return -1;
			}

			const option = anchorEl.querySelector( `[data-tag-index="${ nextFocus }"]` );

			// Same logic as MenuList.js
			if (
				! option ||
				! option.hasAttribute( 'tabindex' ) ||
				option.disabled ||
				option.getAttribute( 'aria-disabled' ) === 'true'
			) {
				nextFocus += direction === 'next' ? 1 : -1;
			} else {
				return nextFocus;
			}
		}
	}

	const handleFocusTag = ( event, direction ) => {
		if ( ! multiple ) {
			return;
		}

		if ( inputValue === '' ) {
			handleClose( event, 'toggleInput' );
		}

		let nextTag = focusedTag;

		if ( focusedTag === -1 ) {
			if ( inputValue === '' && direction === 'previous' ) {
				nextTag = value.length - 1;
			}
		} else {
			nextTag += direction === 'next' ? 1 : -1;

			if ( nextTag < 0 ) {
				nextTag = 0;
			}

			if ( nextTag === value.length ) {
				nextTag = -1;
			}
		}

		nextTag = validTagIndex( nextTag, direction );

		setFocusedTag( nextTag );
		focusTag( nextTag );
	};

	const handleClear = ( event ) => {
		ignoreFocus.current = true;
		setInputValueState( '' );

		if ( onInputChange ) {
			onInputChange( event, '', 'clear' );
		}

		handleValue( event, multiple ? [] : null, 'clear' );
	};

	const handleKeyDown = ( other ) => ( event ) => {
		if ( other.onKeyDown ) {
			other.onKeyDown( event );
		}

		if ( event.defaultMuiPrevented ) {
			return;
		}

		if ( focusedTag !== -1 && ! [ 'ArrowLeft', 'ArrowRight' ].includes( event.key ) ) {
			setFocusedTag( -1 );
			focusTag( -1 );
		}

		// Wait until IME is settled.
		if ( event.which !== 229 ) {
			switch ( event.key ) {
				case 'Home':
					if ( popupOpen && handleHomeEndKeys ) {
						// Prevent scroll of the page
						event.preventDefault();
						changeHighlightedIndex( {
							diff: 'start',
							direction: 'next',
							reason: 'keyboard',
							event,
						} );
					}
					break;
				case 'End':
					if ( popupOpen && handleHomeEndKeys ) {
						// Prevent scroll of the page
						event.preventDefault();
						changeHighlightedIndex( {
							diff: 'end',
							direction: 'previous',
							reason: 'keyboard',
							event,
						} );
					}
					break;
				case 'PageUp':
					// Prevent scroll of the page
					event.preventDefault();
					changeHighlightedIndex( {
						diff: -pageSize,
						direction: 'previous',
						reason: 'keyboard',
						event,
					} );
					handleOpen( event );
					break;
				case 'PageDown':
					// Prevent scroll of the page
					event.preventDefault();
					changeHighlightedIndex( {
						diff: pageSize,
						direction: 'next',
						reason: 'keyboard',
						event,
					} );
					handleOpen( event );
					break;
				case 'ArrowDown':
					// Prevent cursor move
					event.preventDefault();
					changeHighlightedIndex( {
						diff: 1,
						direction: 'next',
						reason: 'keyboard',
						event,
					} );
					handleOpen( event );
					break;
				case 'ArrowUp':
					// Prevent cursor move
					event.preventDefault();
					changeHighlightedIndex( {
						diff: -1,
						direction: 'previous',
						reason: 'keyboard',
						event,
					} );
					handleOpen( event );
					break;
				case 'ArrowLeft':
					handleFocusTag( event, 'previous' );
					break;
				case 'ArrowRight':
					handleFocusTag( event, 'next' );
					break;
				case 'Enter':
					if ( highlightedIndexRef.current !== -1 && popupOpen ) {
						const option = filteredOptions[ highlightedIndexRef.current ];
						const disabled = getOptionDisabled ? getOptionDisabled( option ) : false;

						// Avoid early form validation, let the end-users continue filling the form.
						event.preventDefault();

						if ( disabled ) {
							return;
						}

						selectNewValue( event, option, 'selectOption' );

						// Move the selection to the end.
						if ( autoComplete ) {
							inputRef.current.setSelectionRange(
								inputRef.current.value.length,
								inputRef.current.value.length
							);
						}
					} else if ( inputValue !== '' && inputValueIsSelectedValue === false ) {
						if ( multiple ) {
							// Allow people to add new values before they submit the form.
							event.preventDefault();
						}
						selectNewValue( event, inputValue, 'createOption', 'freeSolo' );
					}
					break;
				case 'Escape':
					if ( popupOpen ) {
						// Avoid Opera to exit fullscreen mode.
						event.preventDefault();
						// Avoid the Modal to handle the event.
						event.stopPropagation();
						handleClose( event, 'escape' );
					} else if (
						clearOnEscape &&
						( inputValue !== '' || ( multiple && value.length > 0 ) )
					) {
						// Avoid Opera to exit fullscreen mode.
						event.preventDefault();
						// Avoid the Modal to handle the event.
						event.stopPropagation();
						handleClear( event );
					}
					break;
				case 'Backspace':
					// Remove the value on the left of the "cursor"
					if ( multiple && ! readOnly && inputValue === '' && value.length > 0 ) {
						const index = focusedTag === -1 ? value.length - 1 : focusedTag;
						const newValue = value.slice();
						newValue.splice( index, 1 );
						handleValue( event, newValue, 'removeOption', {
							option: value[ index ],
						} );
					}
					break;
				case 'Delete':
					// Remove the value on the right of the "cursor"
					if (
						multiple &&
						! readOnly &&
						inputValue === '' &&
						value.length > 0 &&
						focusedTag !== -1
					) {
						const index = focusedTag;
						const newValue = value.slice();
						newValue.splice( index, 1 );
						handleValue( event, newValue, 'removeOption', {
							option: value[ index ],
						} );
					}
					break;
				default:
			}
		}
	};

	const handleFocus = ( event ) => {
		setFocused( true );

		if ( openOnFocus && ! ignoreFocus.current ) {
			handleOpen( event );
		}
	};

	const handleBlur = ( event ) => {
		// Ignore the event when using the scrollbar with IE11
		if ( unstable_isActiveElementInListBox( listboxRef ) ) {
			inputRef.current.focus();
			return;
		}

		setFocused( false );
		firstFocus.current = true;
		ignoreFocus.current = false;

		if ( autoSelect && highlightedIndexRef.current !== -1 && popupOpen ) {
			selectNewValue( event, filteredOptions[ highlightedIndexRef.current ], 'blur' );
		} else if ( autoSelect && freeSolo && inputValue !== '' ) {
			selectNewValue( event, inputValue, 'blur', 'freeSolo' );
		} else if ( clearOnBlur ) {
			resetInputValue( event, value, 'blur' );
		}

		handleClose( event, 'blur' );
	};

	const handleInputChange = ( event ) => {
		const newValue = event.target.value;

		if ( inputValue !== newValue ) {
			setInputValueState( newValue );
			setInputPristine( false );

			if ( onInputChange ) {
				onInputChange( event, newValue, 'input' );
			}
		}

		if ( newValue === '' ) {
			if ( ! disableClearable && ! multiple ) {
				handleValue( event, null, 'clear' );
			}
		} else {
			handleOpen( event );
		}
	};

	const handleOptionMouseMove = ( event ) => {
		const index = Number( event.currentTarget.getAttribute( 'data-option-index' ) );
		if ( highlightedIndexRef.current !== index ) {
			setHighlightedIndex( {
				event,
				index,
				reason: 'mouse',
			} );
		}
	};

	const handleOptionTouchStart = ( event ) => {
		setHighlightedIndex( {
			event,
			index: Number( event.currentTarget.getAttribute( 'data-option-index' ) ),
			reason: 'touch',
		} );
		isTouch.current = true;
	};

	const handleOptionClick = ( event ) => {
		const index = Number( event.currentTarget.getAttribute( 'data-option-index' ) );
		selectNewValue( event, filteredOptions[ index ], 'selectOption' );

		isTouch.current = false;
	};

	const handleTagDelete = ( index ) => ( event ) => {
		const newValue = value.slice();
		newValue.splice( index, 1 );
		handleValue( event, newValue, 'removeOption', {
			option: value[ index ],
		} );
	};

	const handlePopupIndicator = ( event ) => {
		if ( open ) {
			handleClose( event, 'toggleInput' );
		} else {
			handleOpen( event );
		}
	};

	// Prevent input blur when interacting with the combobox
	const handleMouseDown = ( event ) => {
		// Prevent focusing the input if click is anywhere outside the Autocomplete
		if ( ! event.currentTarget.contains( event.target ) ) {
			return;
		}
		if ( event.target.getAttribute( 'id' ) !== id ) {
			event.preventDefault();
		}
	};

	// Focus the input when interacting with the combobox
	const handleClick = ( event ) => {
		// Prevent focusing the input if click is anywhere outside the Autocomplete
		if ( ! event.currentTarget.contains( event.target ) ) {
			return;
		}
		inputRef.current.focus();

		if (
			selectOnFocus &&
			firstFocus.current &&
			inputRef.current.selectionEnd - inputRef.current.selectionStart === 0
		) {
			inputRef.current.select();
		}

		firstFocus.current = false;
	};

	const handleInputMouseDown = ( event ) => {
		if ( ! disabledProp && ( inputValue === '' || ! open ) ) {
			handlePopupIndicator( event );
		}
	};

	if ( disabledProp && focused ) {
		handleBlur();
	}

	return {
		...props,
		getRootProps: ( other = {} ) => ( {
			'aria-owns': listboxAvailable ? `${ id }-listbox` : null,
			...other,
			onKeyDown: handleKeyDown( other ),
			onMouseDown: handleMouseDown,
			onClick: handleClick,
		} ),
		getInputLabelProps: () => ( {
			id: `${ id }-label`,
			htmlFor: id,
		} ),
		getInputProps: () => ( {
			id,
			value: inputValue,
			onBlur: handleBlur,
			onFocus: ( event ) => {
				setFocused( true );

				if ( openOnFocus && ! ignoreFocus.current ) {
					handleOpen( event );
				}
			},
			onChange: handleInputChange,
			onMouseDown: handleInputMouseDown,
			// if open then this is handled imperatively so don't let react override
			// only have an opinion about this when closed
			'aria-activedescendant': popupOpen ? '' : null,
			'aria-autocomplete': autoComplete ? 'both' : 'list',
			'aria-controls': listboxAvailable ? `${ id }-listbox` : undefined,
			'aria-expanded': listboxAvailable,
			// Disable browser's suggestion that might overlap with the popup.
			// Handle autocomplete but not autofill.
			autoComplete: 'off',
			ref: inputRef,
			autoCapitalize: 'none',
			spellCheck: 'false',
			role: 'combobox',
			disabled: disabledProp,
		} ),
		getClearProps: () => ( {
			tabIndex: -1,
			type: 'button',
			onClick: handleClear,
		} ),
		getPopupIndicatorProps: () => ( {
			tabIndex: -1,
			type: 'button',
			onClick: handlePopupIndicator,
		} ),
		getTagProps: ( { index } ) => ( {
			key: index,
			'data-tag-index': index,
			tabIndex: -1,
			...( ! readOnly && { onDelete: handleTagDelete( index ) } ),
		} ),
		getListBoxProps: () => ( {
			role: 'listbox',
			id: `${ id }-listbox`,
			'aria-labelledby': `${ id }-label`,
			ref: handleListBoxRef,
			onMouseDown: ( event ) => {
				// Prevent blur
				event.preventDefault();
			},
		} ),
		getOptionProps: ( { index, option } ) => {
			const selected = ( multiple ? value : [ value ] ).some(
				( value2 ) => value2 !== null && isOptionEqualToValue( option, value2 )
			);
			const disabled = getOptionDisabled ? getOptionDisabled( option ) : false;

			return {
				key: getOptionKey?.( option ) ?? getOptionLabel( option ),
				tabIndex: -1,
				role: 'option',
				id: `${ id }-option-${ index }`,
				onMouseMove: handleOptionMouseMove,
				onClick: handleOptionClick,
				onTouchStart: handleOptionTouchStart,
				'data-option-index': index,
				'aria-disabled': disabled,
				'aria-selected': selected,
			};
		},
		id,
		inputValue,
		value,
		expanded: popupOpen && anchorEl,
		popupOpen,
		focused: focused || focusedTag !== -1,
		anchorEl,
		setAnchorEl,
		focusedTag,
		options: filteredOptions,
	};
}