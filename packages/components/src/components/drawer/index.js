/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Modal } from '@wordpress/components';
import { useRef, forwardRef } from '@wordpress/element';
import { useMergeRefs } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import './style.scss';
const Drawer = forwardRef( ( props, ref ) => {
	const {
		className,
		onClose,
		size,
		shouldCloseOnClickOutside = false,
		style,
		children,
		...rest
	} = props;
	const drawerRef = useRef( null );
	const classes = classnames( 'eac-drawer', className );
	return (
		<Modal
			className={ classes }
			{ ...rest }
			overlayClassName="eac-drawer__overlay"
			onRequestClose={ onClose }
			shouldCloseOnClickOutside={ shouldCloseOnClickOutside }
			style={ { ...style } }
			ref={ useMergeRefs( [ drawerRef, ref ] ) }
		>
			<div className="eac-drawer__body">{ children }</div>
		</Modal>
	);
} );
export default Drawer;
