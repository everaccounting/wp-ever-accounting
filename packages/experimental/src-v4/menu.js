/**
 * WordPress dependencies
 */
import { forwardRef, useRef, useLayoutEffect } from '@wordpress/element';
import {Spinner} from "@eac/components";
/**
 * Internal dependencies
 */
import { MenuContainer, SearchInput, Options, Option } from './styles';

const Menu = forwardRef((props, ref) => {
	const { isLoading, options, inputValue, isSearchable, getOptionLabel, getOptionValue } = props;

	// ==============================
	// Refs
	// ------------------------------
	const optionsRef = useRef();

	// ==============================
	// Hooks
	// ==============================
	//set the active option to the first option when the menu is opened.
	useLayoutEffect(() => {
		const $active = getHoveredOption();
		if ($active) {
			$active.removeAttribute('data-hover');
		}
		if (optionsRef.current.firstElementChild) {
			optionsRef.current.firstElementChild.setAttribute('data-hover', 'true');
			// scroll the active option into view.
			//getHoveredOption().scrollIntoView({ block: 'nearest' });
		}
	}, [options]);

	// ==============================
	// Methods
	// ==============================
	const getHoveredOption = () => optionsRef.current?.querySelector('[data-hover="true"]');
	const onHoverOption = (e) => {
		const $active = getHoveredOption();
		if ($active) {
			$active.removeAttribute('data-hover');
		}
		e.target.setAttribute('data-hover', 'true');
	};

	return (
		<MenuContainer ref={ref} role="listbox" width={props.width}>
			{isSearchable && (
				<div className="eac-select-control__input-container">
					<SearchInput
						className="eac-select-control__input"
						ref={props.inputRef}
						type="text"
						placeholder={props.placeholder}
						onChange={(e) => props.onInputChange(e.target.value)}
						value={props.inputValue}
					/>
					{isLoading && <Spinner />}
				</div>
			)}
			<Options ref={optionsRef}>
				{options &&
					options.length > 0 &&
					options.map((option, index) => (
						<Option
							key={getOptionValue(option) || index}
							onClick={() => props.onOptionClick(option)}
							onMouseEnter={onHoverOption}
						>
							{getOptionLabel(option)}
						</Option>
					))}
				{(!options || options.length === 0) && <Option>No results</Option>}
			</Options>
		</MenuContainer>
	);
});

export default Menu;
