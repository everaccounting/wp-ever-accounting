/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import {
	DropdownMenu as _DropdownMenu,
	MenuGroup,
	MenuItem,
} from '@wordpress/components';

function DropdownMenu( props ) {
	const { className } = props;
	const classes = classnames( 'eac-dropdown-menu', className );
	return <_DropdownMenu { ...props } className={ classes } />;
}

export default DropdownMenu;
