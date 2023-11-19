/**
 * External dependencies
 */
import Portal from '@rc-component/portal';
import useLayoutEffect from 'rc-util/lib/hooks/useLayoutEffect';
/**
 * Internal dependencies
 */
import { RefContext } from './context';
import DrawerPopup from './DrawerPopup';
import { warnCheck } from './util';
const Drawer = ( props ) => {
	const {
		open = false,
		prefixCls = 'rc-drawer',
		placement = 'right',
		autoFocus = true,
		keyboard = true,
		width = 378,
		mask = true,
		maskClosable = true,
		getContainer,
		forceRender,
		afterOpenChange,
		destroyOnClose,
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
		// Refs
		panelRef,
	} = props;
	const [ animatedVisible, setAnimatedVisible ] = React.useState( false );
	// ============================= Warn =============================
	// ============================= Open =============================
	const [ mounted, setMounted ] = React.useState( false );
	useLayoutEffect( () => {
		setMounted( true );
	}, [] );
	const mergedOpen = mounted ? open : false;
	// ============================ Focus =============================
	const popupRef = React.useRef();
	const lastActiveRef = React.useRef();
	useLayoutEffect( () => {
		if ( mergedOpen ) {
			lastActiveRef.current = document.activeElement;
		}
	}, [ mergedOpen ] );
	// ============================= Open =============================
	const internalAfterOpenChange = ( nextVisible ) => {
		setAnimatedVisible( nextVisible );
		afterOpenChange?.( nextVisible );
		if (
			! nextVisible &&
			lastActiveRef.current &&
			! popupRef.current?.contains( lastActiveRef.current )
		) {
			lastActiveRef.current?.focus( { preventScroll: true } );
		}
	};
	// =========================== Context ============================
	const refContext = React.useMemo(
		() => ( {
			panel: panelRef,
		} ),
		[ panelRef ]
	);
	// ============================ Render ============================
	if ( ! forceRender && ! animatedVisible && ! mergedOpen && destroyOnClose ) {
		return null;
	}
	const eventHandlers = {
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
	};
	const drawerPopupProps = {
		...props,
		open: mergedOpen,
		prefixCls,
		placement,
		autoFocus,
		keyboard,
		width,
		mask,
		maskClosable,
		inline: getContainer === false,
		afterOpenChange: internalAfterOpenChange,
		ref: popupRef,
		...eventHandlers,
	};
	return (
		<RefContext.Provider value={ refContext }>
			<Portal
				open={ mergedOpen || forceRender || animatedVisible }
				autoDestroy={ false }
				getContainer={ getContainer }
				autoLock={ mask && ( mergedOpen || animatedVisible ) }
			>
				<DrawerPopup { ...drawerPopupProps } />
			</Portal>
		</RefContext.Provider>
	);
};
if ( process.env.NODE_ENV !== 'production' ) {
	Drawer.displayName = 'Drawer';
}
export default Drawer;
