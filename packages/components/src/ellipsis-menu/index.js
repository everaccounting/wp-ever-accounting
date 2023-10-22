/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Button, Dropdown, NavigableMenu } from '@wordpress/components';
import { Icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import MenuItem from './menu-item';
import MenuTitle from './menu-title';

export { MenuItem, MenuTitle };

import './style.scss';

const EllipsisMenu = ({ label, renderContent, className, onToggle }) => {
	if (!renderContent) {
		return null;
	}

	const renderEllipsis = ({ onToggle: toggleHandlerOverride, isOpen }) => {
		const toggleClassname = classnames('eac-ellipsis-menu__toggle', {
			'is-opened': isOpen,
		});

		return (
			<Button
				className={toggleClassname}
				onClick={(e) => {
					if (onToggle) {
						onToggle(e);
					}
					if (toggleHandlerOverride) {
						toggleHandlerOverride();
					}
				}}
				title={label}
				aria-expanded={isOpen}
			>
				<Icon icon="ellipsis" size={16} />
			</Button>
		);
	};

	const renderMenu = (renderContentArgs) => (
		<NavigableMenu className="eac-ellipsis-menu__content">{renderContent(renderContentArgs)}</NavigableMenu>
	);

	return (
		<div className={classnames(className, 'eac-ellipsis-menu')}>
			<Dropdown
				contentClassName="eac-ellipsis-menu__popover"
				position="bottom left"
				renderToggle={renderEllipsis}
				renderContent={renderMenu}
			/>
		</div>
	);
};

EllipsisMenu.Title = MenuTitle;
EllipsisMenu.Item = MenuItem;

export default EllipsisMenu;
