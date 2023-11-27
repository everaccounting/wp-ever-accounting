/**
 * External dependencies
 */
import * as React from 'react';
import classNames from 'classnames';
import RcDrawer from 'rc-drawer';
/**
 * Internal dependencies
 */
import { getTransitionName } from '../_util/motion';
import { devUseWarning } from '../_util/warning';
import { ConfigContext } from '../config-provider';
import { NoFormStyle } from '../form/context';
// CSSINJS
import { NoCompactStyle } from '../space/Compact';
import { usePanelRef } from '../watermark/context';
import DrawerPanel from './DrawerPanel';
import useStyle from './style';

const SizeTypes = [ 'default', 'large' ];
const defaultPushState = { distance: 180 };
const Drawer = ( props ) => {
	const {
		rootClassName,
		width,
		height,
		size = 'default',
		mask = true,
		push = defaultPushState,
		open,
		afterOpenChange,
		onClose,
		prefixCls: customizePrefixCls,
		getContainer: customizeGetContainer,
		style,
		className,
		// Deprecated
		visible,
		afterVisibleChange,
		...rest
	} = props;
	const { getPopupContainer, getPrefixCls, direction, drawer } =
		React.useContext( ConfigContext );
	const prefixCls = getPrefixCls( 'drawer', customizePrefixCls );
	// Style
	const [ wrapSSR, hashId ] = useStyle( prefixCls );
	const getContainer =
		// 有可能为 false，所以不能直接判断
		customizeGetContainer === undefined && getPopupContainer
			? () => getPopupContainer( document.body )
			: customizeGetContainer;
	const drawerClassName = classNames(
		{
			'no-mask': ! mask,
			[ `${ prefixCls }-rtl` ]: direction === 'rtl',
		},
		rootClassName,
		hashId
	);
	// ============================ Size ============================
	const mergedWidth = React.useMemo(
		() => width ?? ( size === 'large' ? 736 : 378 ),
		[ width, size ]
	);
	const mergedHeight = React.useMemo(
		() => height ?? ( size === 'large' ? 736 : 378 ),
		[ height, size ]
	);
	// =========================== Motion ===========================
	const maskMotion = {
		motionName: getTransitionName( prefixCls, 'mask-motion' ),
		motionAppear: true,
		motionEnter: true,
		motionLeave: true,
		motionDeadline: 500,
	};
	const panelMotion = ( motionPlacement ) => ( {
		motionName: getTransitionName( prefixCls, `panel-motion-${ motionPlacement }` ),
		motionAppear: true,
		motionEnter: true,
		motionLeave: true,
		motionDeadline: 500,
	} );
	// ============================ Refs ============================
	// Select `ant-modal-content` by `panelRef`
	const panelRef = usePanelRef();
	// =========================== Render ===========================
	return wrapSSR(
		<NoCompactStyle>
			<NoFormStyle status override>
				<RcDrawer
					prefixCls={ prefixCls }
					onClose={ onClose }
					maskMotion={ maskMotion }
					motion={ panelMotion }
					{ ...rest }
					open={ open ?? visible }
					mask={ mask }
					push={ push }
					width={ mergedWidth }
					height={ mergedHeight }
					style={ { ...drawer?.style, ...style } }
					className={ classNames( drawer?.className, className ) }
					rootClassName={ drawerClassName }
					getContainer={ getContainer }
					afterOpenChange={ afterOpenChange ?? afterVisibleChange }
					panelRef={ panelRef }
				>
					<DrawerPanel prefixCls={ prefixCls } { ...rest } onClose={ onClose } />
				</RcDrawer>
			</NoFormStyle>
		</NoCompactStyle>
	);
};
 * @private
const PurePanel = ( props ) => {
	const {
		prefixCls: customizePrefixCls,
		style,
		className,
		placement = 'right',
		...restProps
	} = props;
	const { getPrefixCls } = React.useContext( ConfigContext );
	const prefixCls = getPrefixCls( 'drawer', customizePrefixCls );
	// Style
	const [ wrapSSR, hashId ] = useStyle( prefixCls );
	const cls = classNames(
		prefixCls,
		`${ prefixCls }-pure`,
		`${ prefixCls }-${ placement }`,
		hashId,
		className
	);
	return wrapSSR(
		<div className={ cls } style={ style }>
			<DrawerPanel prefixCls={ prefixCls } { ...restProps } />
		</div>
	);
};
Drawer._InternalPanelDoNotUseOrYouWillBeFired = PurePanel;
if ( process.env.NODE_ENV !== 'production' ) {
	Drawer.displayName = 'Drawer';
}
export default Drawer;
