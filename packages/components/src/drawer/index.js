/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import RcDrawer from 'rc-drawer';
/**
 * Internal dependencies
 */
import './style.scss';

import DrawerPanel from './drawer-panel';
const defaultPushState = { distance: 180 };

function Drawer(props) {
	const {
		width,
		height,
		size = 'default',
		mask = true,
		push = defaultPushState,
		open,
		afterOpenChange,
		onClose,
		getContainer: customizeGetContainer,
		style,
		className,
		// Deprecated
		visible,
		afterVisibleChange,
		...rest
	} = props;

	const getContainer = getContainer ?? document.body;
	const classes = classNames('eac-drawer', {
		'no-mask': !mask,
	});

	const mergedWidth = useMemo(() => width ?? (size === 'large' ? 736 : 378), [width, size]);
	const mergedHeight = useMemo(() => height ?? (size === 'large' ? 736 : 378), [height, size]);
	const maskMotion = {
		motionName: 'eac-drawer-mask-motion',
		motionAppear: true,
		motionEnter: true,
		motionLeave: true,
		motionDeadline: 500,
	};

	const panelMotion = (motionPlacement) => ({
		motionName: `eac-drawer-panel-motion-${motionPlacement}`,
		motionAppear: true,
		motionEnter: true,
		motionLeave: true,
		motionDeadline: 500,
	});

	return (
		<RcDrawer
			prefixCls="eac-drawer"
			onClose={onClose}
			maskMotion={maskMotion}
			motion={panelMotion}
			{...rest}
			open={open ?? visible}
			mask={mask}
			push={push}
			width={mergedWidth}
			height={mergedHeight}
			style={style}
			className={className}
			rootClassName={classes}
			getContainer={getContainer}
			afterOpenChange={afterOpenChange ?? afterVisibleChange}
		>
			<DrawerPanel {...rest} onClose={onClose} />
		</RcDrawer>
	);
}

export default Drawer;
