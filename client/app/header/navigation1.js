/**
 * WordPress dependencies
 */
import { NavigableMenu, Button } from '@wordpress/components';
export default function Navigation1(props ) {
	return (
		<div className="eaccounting-navigation-panel">
			<div className="eaccounting-layout__header-navbar-inner">
				<NavigableMenu
					role="tablist"
					orientation="horizontal"
					className="eaccounting-layout__header-navbar-menu"
				>
					<Button
						role="tab"
						className="eaccounting-layout__header-navbar-menu-item"
					>
						Hello
					</Button>
				</NavigableMenu>
			</div>
		</div>
	);
}
