/**
 * External dependencies
 */
import classnames from 'classnames';

function Panel( props ) {
	const {
		id,
		className,
		style,
		children,
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
	} = props;
	const eventHandlers = {
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
	};
	const classes = classnames( 'eac-drawer__content', className );
	return (
		<div
			id={ id }
			className={ classes }
			style={ style }
			aria-modal="true"
			role="dialog"
			{ ...eventHandlers }
		>
			{ children }
		</div>
	);
}

export default Panel;
