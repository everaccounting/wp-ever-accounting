/**
 * External dependencies
 */
import classnames from 'classnames';
import CSSMotion from 'rc-motion';
/**
 * WordPress dependencies
 */
import {
	useState,
	useContext,
	useRef,
	useEffect,
	useMemo,
	forwardRef,
	useImperativeHandle,
	createPortal,
	useLayoutEffect,
} from '@wordpress/element';
import { Fill, Icon, Button, Animate } from '@wordpress/components';
import {
	useInstanceId,
	useFocusReturn,
	useFocusOnMount,
	useConstrainedTabbing,
	useMergeRefs,
} from '@wordpress/compose';
import { TAB, ESCAPE } from '@wordpress/keycodes';
import { __ } from '@wordpress/i18n';
import { close } from '@wordpress/icons';
/**
 * Internal dependencies
 */
import { RefContext, DrawerContext } from './context';
import Panel from './panel';
import './style.scss';

const sentinelStyle = {
	width: 0,
	height: 0,
	overflow: 'hidden',
	outline: 'none',
	position: 'absolute',
};

function UnforwardedDrawer( props, forwardedRef ) {
	const {
		className,
		width,
		height,
		size = 'default',
		placement = 'right',
		// Mask
		mask = true,
		maskClosable,
		maskClassName,
		maskStyle,
		push = { distance: 180 },
		keyboard,
		open = true,
		afterOpenChange,
		onClose,
		autoFocus,
		style,
		children,
	} = props;

	const instanceId = useInstanceId( Drawer );
	const [ animatedVisible, setAnimatedVisible ] = useState( false );
	// ============================= Warn =============================
	// ============================= Open =============================
	const [ mounted, setMounted ] = useState( false );
	useLayoutEffect( () => {
		setMounted( true );
	}, [] );
	const mergedOpen = mounted ? open : false;

	// ============================ Size ============================
	const mergedWidth = useMemo( () => width ?? ( size === 'large' ? 736 : 378 ), [ width, size ] );
	const mergedHeight = useMemo(
		() => height ?? ( size === 'large' ? 736 : 378 ),
		[ height, size ]
	);
	// ================================ Refs ================================
	const ref = useRef();
	const sentinelStartRef = useRef();
	const sentinelEndRef = useRef();
	useImperativeHandle( ref, () => ref.current );
	const onPanelKeyDown = ( event ) => {
		const { keyCode, shiftKey } = event;
		switch ( keyCode ) {
			// Tab active
			case TAB: {
				if ( keyCode === TAB ) {
					if ( ! shiftKey && document.activeElement === sentinelEndRef.current ) {
						sentinelStartRef.current?.focus( { preventScroll: true } );
					} else if ( shiftKey && document.activeElement === sentinelStartRef.current ) {
						sentinelEndRef.current?.focus( { preventScroll: true } );
					}
				}
				break;
			}
			// Close
			case ESCAPE: {
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
	useEffect( () => {
		if ( open && autoFocus ) {
			ref.current?.focus( { preventScroll: true } );
		}
	}, [ autoFocus, open ] );
	// ============================ Push ============================
	const [ pushed, setPushed ] = useState( false );
	const parentContext = useContext( DrawerContext );
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
	const mergedContext = useMemo(
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
	useEffect( () => {
		if ( open ) {
			parentContext?.push?.();
		} else {
			parentContext?.pull?.();
		}
	}, [ open, parentContext ] );
	// Clean up
	useEffect(
		() => () => {
			parentContext?.pull?.();
		},
		[ parentContext ]
	);
	// =========================== Motion ===========================
	const maskMotion = {
		motionName: 'eac-drawer__mask-motion',
		motionAppear: true,
		motionEnter: true,
		motionLeave: true,
		motionDeadline: 500,
	};
	// =========================== Context ============================
	const refContext = {};
	// =========================== Panel ============================
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
		wrapperStyle.width = Number( width );
	} else {
		wrapperStyle.height = Number( height );
	}

	const maskNode = mask && (
		<CSSMotion key="mask" visible={ open } { ...maskMotion }>
			{ ( { className: motionMaskClassName, style: motionMaskStyle }, maskRef ) => {
				return (
					<div
						key="mask"
						aria-hidden="true"
						tabIndex={ -1 }
						className={ classnames( 'eac-drawer__mask', motionMaskClassName ) }
						onClick={ open ? onClose : undefined }
						style={ { ...motionMaskStyle } }
						ref={ maskRef }
					/>
				);
			} }
		</CSSMotion>
	);

	const eventHandlers = {
		// onMouseEnter,
		// onMouseOver,
		// onMouseLeave,
		// onClick,
		// onKeyDown,
		// onKeyUp,
	};
	const panelNode = mask && (
		<Animate
			type="slide-in"
			options={ {
				origin: 'left',
			} }
		>
			{ ( animate ) => {
				return (
					<div
						className={ classnames( 'eac-drawer__content-wrapper', animate.className ) }
					>
						<Panel { ...eventHandlers }>{ children }</Panel>
					</div>
				);
			} }
		</Animate>
	);

	return createPortal(
		<div>
			<RefContext.Provider value={ refContext }>
				<DrawerContext.Provider value={ mergedContext }>
					<div
						className={ classnames( 'eac-drawer', className, {
							'eac-drawer--open': mergedOpen,
							'eac-drawer--close': ! mergedOpen,
							'eac-drawer--push': pushed,
							'eac-drawer--no-mask': ! mask,
							[ `eac-drawer--${ placement }` ]: placement,
						} ) }
						style={ style }
						role="dialog"
						aria-modal="true"
						aria-labelledby={ `eac-drawer-title-${ instanceId }` }
						aria-describedby={ `eac-drawer-content-${ instanceId }` }
						aria-hidden={ ! open }
						tabIndex={ -1 }
						ref={ useMergeRefs( [ ref, forwardedRef ] ) }
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
			</RefContext.Provider>
		</div>,
		document.body
	);
}

export const Drawer = forwardRef( UnforwardedDrawer );

export default Drawer;
