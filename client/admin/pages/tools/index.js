/**
 * External dependencies
 */
import { Input, SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { DropdownMenu } from '@wordpress/components';
import {
	more,
	arrowLeft,
	arrowRight,
	arrowUp,
	arrowDown,
} from '@wordpress/icons';

function Tools() {
	return (
		<>
			<SectionHeader card title={ __( 'Tools', 'wp-ever-accounting' ) } />
			<DropdownMenu
				className="eac-dropdown-menu"
				label="Select a direction"
				toggleProps={ {
					icon: more,
					isPrimary: true,
					className: 'eac-dropdown-menu__toggle',
					children: <>Title</>,
				} }
				controls={ [
					{
						title: 'Up',
						icon: arrowUp,
						onClick: () => console.log( 'up' ),
					},
					{
						title: 'Right',
						icon: arrowRight,
						onClick: () => console.log( 'right' ),
					},
					{
						title: 'Down',
						icon: arrowDown,
						onClick: () => console.log( 'down' ),
					},
					{
						title: 'Left',
						icon: arrowLeft,
						onClick: () => console.log( 'left' ),
					},
				] }
			/>
		</>
	);
}

export default Tools;
