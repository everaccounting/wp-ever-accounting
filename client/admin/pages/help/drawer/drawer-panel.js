/**
 * External dependencies
 */
import classNames from 'classnames';

const DrawerPanel = (props) => {
	const {
		className,
		style,
		children,
		containerRef,
		id,
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
	return (
		<div
			id={id}
			className={classNames(`eac-drawer-content`, className)}
			style={{
				...style,
			}}
			aria-modal="true"
			role="dialog"
			ref={containerRef}
			{...eventHandlers}
		>
			{children}
		</div>
	);
};

export default DrawerPanel;
