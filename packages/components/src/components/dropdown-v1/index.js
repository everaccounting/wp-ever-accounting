/**
 * WordPress dependencies
 */
import {
	Button,
	Dropdown as DropdownComponent,
	MenuGroup,
	NavigableMenu,
} from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import MenuTitle from './menu-title';
import MenuItem from './menu-item';
import MenuSeparator from './menu-separator';
import './style.scss';

function Dropdown( props ) {
	const { className, children, renderContent, buttonProps, label, ...rest } = props;

	const renderDropdown = ( { onToggle: _onToggle, isOpen } ) => {
		const toggleClassname = classnames( 'eac-dropdown__button', {
			'is-opened': isOpen,
		} );

		return (
			<Button
				className={ toggleClassname }
				isPressed={ isOpen }
				onClick={ _onToggle }
				aria-expanded={ isOpen }
				isSecondary={ true }
				iconSize={ 16 }
				size="compact"
				style={ {
					width: 'unset',
					flexDirection: 'row-reverse',
				} }
				icon="ellipsis"
				iconPosition="right"
				label={ label }
				{ ...buttonProps }
			>
				{ label && <span className="eac-dropdown__title">{ label }</span> }
			</Button>
		);
	};

	const renderContentMenu = ( renderContentArgs ) => (
		<NavigableMenu className="eac-dropdown__content">
			{ renderContent?.( renderContentArgs ) }
			{ children }
		</NavigableMenu>
	);

	return (
		<DropdownComponent
			className={ classnames( className, 'eac-dropdown' ) }
			popoverProps={ {
				focusOnMount: true,
			} }
			contentClassName="eac-dropdown__popover"
			renderToggle={ renderDropdown }
			renderContent={ renderContentMenu }
			{ ...rest }
		/>
	);
}

Dropdown.Group = MenuGroup;
Dropdown.Item = MenuItem;
Dropdown.Title = MenuTitle;
Dropdown.Separator = MenuSeparator;
export default Dropdown;
