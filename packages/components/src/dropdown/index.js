/**
 * WordPress dependencies
 */
import { Button, Dropdown as DropdownComponent } from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';

const Dropdown = ( props ) => {
	const {
		label,
		icon = 'ellipsis',
		splitButton,
		children,
		menu,
		isOpen,
		...otherProps
	} = props;
	const classes = classnames( 'eac-dropdown', {
		'eac-dropdown--open': isOpen,
	} );

	// All I want to do is, when menu is passed and clicked on the menu item, the menu should close.
	// When clicked on child, the menu should not close. we will send the onToggle prop to the child.

	const renderButton = ( { onToggle: onButtonClick, isOpen: _isOpen } ) => {
		return (
			<Button
				className="eac-dropdown__toggle"
				onClick={ onButtonClick }
				aria-expanded={ _isOpen }
				icon={ icon }
			>
				{ label }
			</Button>
		);
	};

	const renderContent = ( renderContentArgs ) => {
		return <>{ children && children( renderContentArgs ) }</>;
	};

	return (
		<DropdownComponent
			className={ classes }
			contentClassName="eac-dropdown__popover"
			position="bottom left"
			renderToggle={ renderButton }
			renderContent={ renderContent }
		/>
	);
};

export default Dropdown;
