/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useMemo, useCallback } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { useClosable } from '../hooks/use-closeable';

function DrawerPanel(props) {
	const {
		title,
		footer,
		extra,
		closeIcon,
		closable,
		onClose,
		headerStyle,
		drawerStyle,
		bodyStyle,
		footerStyle,
		children,
	} = props;
	const customCloseIconRender = useCallback(
		(icon) => (
			<button type="button" onClick={onClose} aria-label="Close" className={`eac-drawer-close`}>
				{icon}
			</button>
		),
		[onClose]
	);
	const [mergedClosable, mergedCloseIcon] = useClosable(closable, closeIcon, customCloseIconRender, undefined, true);
	const headerNode = useMemo(() => {
		if (!title && !mergedClosable) {
			return null;
		}
		return (
			<div
				style={headerStyle}
				className={classNames(`eac-drawer-header`, {
					[`eac-drawer-header-close-only`]: mergedClosable && !title && !extra,
				})}
			>
				<div className={`eac-drawer-header-title`}>
					{mergedCloseIcon}
					{title && <div className={`eac-drawer-title`}>{title}</div>}
				</div>
				{extra && <div className={`eac-drawer-extra`}>{extra}</div>}
			</div>
		);
	}, [mergedClosable, mergedCloseIcon, extra, headerStyle, title]);
	const footerNode = useMemo(() => {
		if (!footer) {
			return null;
		}
		const footerClassName = `eac-drawer-footer`;
		return (
			<div className={footerClassName} style={footerStyle}>
				{footer}
			</div>
		);
	}, [footer, footerStyle]);
	return (
		<div className={`eac-drawer-wrapper-body`} style={drawerStyle}>
			{headerNode}
			<div className={`eac-drawer-body`} style={bodyStyle}>
				{children}
			</div>
			{footerNode}
		</div>
	);
}

export default DrawerPanel;
