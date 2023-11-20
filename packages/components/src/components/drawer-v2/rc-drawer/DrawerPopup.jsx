/**
 * External dependencies
 */
import classNames from 'classnames';
import CSSMotion from 'rc-motion';
import KeyCode from 'rc-util/lib/KeyCode';
import pickAttrs from 'rc-util/lib/pickAttrs';
import * as React from 'react';
/**
 * Internal dependencies
 */
import DrawerContext from './context';
import DrawerPanel from './DrawerPanel';
import { parseWidthHeight } from './util';

const sentinelStyle = {
	width: 0,
	height: 0,
	overflow: 'hidden',
	outline: 'none',
	position: 'absolute',
};

function DrawerPopup( props, ref ) {
	const {
		prefixCls,
		open,
		placement,
		inline,
		push,
		forceRender,
		autoFocus,
		keyboard,
		// classNames
		classNames: drawerClassNames,
		// Root
		rootClassName,
		rootStyle,
		zIndex,
		// Drawer
		className,
		id,
		style,
		motion,
		width,
		height,
		children,
		contentWrapperStyle,
		// Mask
		mask,
		maskClosable,
		maskMotion,
		maskClassName,
		maskStyle,
		// Events
		afterOpenChange,
		onClose,
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
		styles,
	} = props;
	// ================================ Refs ================================
	const panelRef = React.useRef();
	const sentinelStartRef = React.useRef();
	const sentinelEndRef = React.useRef();
	React.useImperativeHandle( ref, () => panelRef.current );
	const onPanelKeyDown = ( event ) => {
		const { keyCode, shiftKey } = event;
		switch ( keyCode ) {
			// Tab active
			case KeyCode.TAB: {
				if ( keyCode === KeyCode.TAB ) {
					if ( ! shiftKey && document.activeElement === sentinelEndRef.current ) {
						sentinelStartRef.current?.focus( { preventScroll: true } );
					} else if ( shiftKey && document.activeElement === sentinelStartRef.current ) {
						sentinelEndRef.current?.focus( { preventScroll: true } );
					}
				}
				break;
			}
			// Close
			case KeyCode.ESC: {
				if ( onClose && keyboard ) {
					event.stopPropagation();
					onClose( event );
				}
				break;
			}
		}
	};
	// ========================== Control ===========================
	// Auto Focus
	React.useEffect( () => {
		if ( open && autoFocus ) {
			panelRef.current?.focus( { preventScroll: true } );
		}
	}, [ autoFocus, open ] );
	// ============================ Push ============================
	const [ pushed, setPushed ] = React.useState( false );
	const parentContext = React.useContext( DrawerContext );
	// Merge push distance
	let pushConfig;
	if ( push === false ) {
		pushConfig = {
			distance: 0,
		};
	} else if ( push === true ) {
		pushConfig = {};
	} else {
		pushConfig = push || {};
	}
	const pushDistance = pushConfig?.distance ?? parentContext?.pushDistance ?? 180;
	const mergedContext = React.useMemo(
		() => ( {
			pushDistance,
			push: () => {
				setPushed( true );
			},
			pull: () => {
				setPushed( false );
			},
		} ),
		[ pushDistance ]
	);
	// ========================= ScrollLock =========================
	// Tell parent to push
	React.useEffect( () => {
		if ( open ) {
			parentContext?.push?.();
		} else {
			parentContext?.pull?.();
		}
	}, [ open, parentContext ] );
	// Clean up
	React.useEffect(
		() => () => {
			parentContext?.pull?.();
		},
		[ parentContext ]
	);
	// ============================ Mask ============================
	const maskNode = mask && (
		<CSSMotion key="mask" { ...maskMotion } visible={ open }>
			{ ( { className: motionMaskClassName, style: motionMaskStyle }, maskRef ) => {
				return (
					<div
						className={ classNames(
							`${ prefixCls }-mask`,
							motionMaskClassName,
							drawerClassNames?.mask,
							maskClassName
						) }
						style={ {
							...motionMaskStyle,
							...maskStyle,
							...styles?.mask,
						} }
						onClick={ maskClosable && open ? onClose : undefined }
						ref={ maskRef }
					/>
				);
			} }
		</CSSMotion>
	);
	// =========================== Panel ============================
	const motionProps = typeof motion === 'function' ? motion( placement ) : motion;
	const wrapperStyle = {};
	if ( pushed && pushDistance ) {
		switch ( placement ) {
			case 'top':
				wrapperStyle.transform = `translateY(${ pushDistance }px)`;
				break;
			case 'bottom':
				wrapperStyle.transform = `translateY(${ -pushDistance }px)`;
				break;
			case 'left':
				wrapperStyle.transform = `translateX(${ pushDistance }px)`;
				break;
			default:
				wrapperStyle.transform = `translateX(${ -pushDistance }px)`;
				break;
		}
	}
	if ( placement === 'left' || placement === 'right' ) {
		wrapperStyle.width = parseWidthHeight( width );
	} else {
		wrapperStyle.height = parseWidthHeight( height );
	}
	const eventHandlers = {
		onMouseEnter,
		onMouseOver,
		onMouseLeave,
		onClick,
		onKeyDown,
		onKeyUp,
	};
	const panelNode = (
		<CSSMotion
			key="panel"
			{ ...motionProps }
			visible={ open }
			forceRender={ forceRender }
			onVisibleChanged={ ( nextVisible ) => {
				afterOpenChange?.( nextVisible );
			} }
			removeOnLeave={ false }
			leavedClassName={ `${ prefixCls }-content-wrapper-hidden` }
		>
			{ ( { className: motionClassName, style: motionStyle }, motionRef ) => {
				return (
					<div
						className={ classNames(
							`${ prefixCls }-content-wrapper`,
							drawerClassNames?.wrapper,
							motionClassName
						) }
						style={ {
							...wrapperStyle,
							...motionStyle,
							...contentWrapperStyle,
							...styles?.wrapper,
						} }
						{ ...pickAttrs( props, { data: true } ) }
					>
						<DrawerPanel
							id={ id }
							containerRef={ motionRef }
							prefixCls={ prefixCls }
							className={ classNames( className, drawerClassNames?.content ) }
							style={ {
								...style,
								...styles?.content,
							} }
							{ ...eventHandlers }
						>
							{ children }
						</DrawerPanel>
					</div>
				);
			} }
		</CSSMotion>
	);
	// =========================== Render ===========================
	const containerStyle = {
		...rootStyle,
	};
	if ( zIndex ) {
		containerStyle.zIndex = zIndex;
	}
	return (
		<DrawerContext.Provider value={ mergedContext }>
			<div
				className={ classNames( prefixCls, `${ prefixCls }-${ placement }`, rootClassName, {
					[ `${ prefixCls }-open` ]: open,
					[ `${ prefixCls }-inline` ]: inline,
				} ) }
				style={ containerStyle }
				tabIndex={ -1 }
				ref={ panelRef }
				onKeyDown={ onPanelKeyDown }
			>
				{ maskNode }
				<div
					tabIndex={ 0 }
					ref={ sentinelStartRef }
					style={ sentinelStyle }
					aria-hidden="true"
					data-sentinel="start"
				/>
				{ panelNode }
				<div
					tabIndex={ 0 }
					ref={ sentinelEndRef }
					style={ sentinelStyle }
					aria-hidden="true"
					data-sentinel="end"
				/>
			</div>
		</DrawerContext.Provider>
	);
}

const RefDrawerPopup = React.forwardRef( DrawerPopup );
if ( process.env.NODE_ENV !== 'production' ) {
	RefDrawerPopup.displayName = 'DrawerPopup';
}
export default RefDrawerPopup;
