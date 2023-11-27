/**
 * WordPress dependencies
 */
import { Dropdown as DropdownComponent, MenuGroup, NavigableMenu } from '@wordpress/components';
import { cloneElement } from '@wordpress/element';
/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import Button from '../button';
import MenuTitle from './menu-title';
import MenuItem from './menu-item';
import MenuSeparator from './menu-separator';
import './style.scss';

function Dropdown( props ) {
	const { className, children, renderToggle, renderContent, ...rest } = props;

	const renderButton = ( { onToggle: _onToggle, isOpen } ) => {
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
				{ ...rest }
			>
				Hey
			</Button>
		);
	};

	return (
		<DropdownComponent
			className={ classnames( className, 'eac-dropdown' ) }
			popoverProps={ {
				focusOnMount: true,
			} }
			contentClassName="eac-dropdown__popover"
			renderToggle={ renderButton }
			renderContent={ ( renderContentArgs ) => (
				<NavigableMenu className="eac-dropdown__content">
					{ renderContent?.( renderContentArgs ) }
					{ children && cloneElement( children, renderContentArgs ) }
				</NavigableMenu>
			) }
			{ ...rest }
		/>
	);
}

Dropdown.Button = Button;
Dropdown.Group = MenuGroup;
Dropdown.Item = MenuItem;
Dropdown.Title = MenuTitle;
Dropdown.Separator = MenuSeparator;
export default Dropdown;
