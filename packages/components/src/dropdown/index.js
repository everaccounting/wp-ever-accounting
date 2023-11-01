/**
 * WordPress dependencies
 */
import { Button, Dropdown as DropdownComponent } from '@wordpress/components';
/**
 * Internal dependencies
 */
import './style.scss';
import ClickOutside from '../click-outside';

const Dropdown = ( props ) => {
	const {
		label,
		icon = 'ellipsis',
		renderContent: _renderContent,
		children,
		buttonProps,
		...otherProps
	} = props;

	const renderButton = ( { onToggle: onButtonClick, isOpen: _isOpen } ) => {
		return (
			<ClickOutside onOutside={ () => _isOpen ?onButtonClick():null }>
				<Button
					className="eac-dropdown__toggle"
					onClick={ onButtonClick }
					aria-expanded={ _isOpen }
					icon={ icon }
					{ ...buttonProps }
				>
					{ label }
				</Button>
			</ClickOutside>
		);
	};

	const renderContent = ( contentProps ) => {
		return (
			<>
				{ children &&
					( typeof children === 'function'
						? children( contentProps )
						: children ) }
				{ _renderContent && _renderContent( contentProps ) }
			</>
		);
	};

	return (
		<DropdownComponent
			className="eac-dropdown"
			contentClassName="eac-dropdown__popover"
			renderToggle={ renderButton }
			renderContent={ renderContent }
			{ ...otherProps }
		/>
	);
};

export default Dropdown;
