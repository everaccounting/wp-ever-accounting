/**
 * External dependencies
 */
import classNames from 'classnames';
import * as React from 'react';
/**
 * Internal dependencies
 */
import useClosable from '../_util/hooks/useClosable';

const DrawerPanel = ( props ) => {
	const {
		prefixCls,
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
	const customCloseIconRender = React.useCallback(
		( icon ) => (
			<button
				type="button"
				onClick={ onClose }
				aria-label="Close"
				className={ `${ prefixCls }-close` }
			>
				{ icon }
			</button>
		),
		[ onClose ]
	);
	const [ mergedClosable, mergedCloseIcon ] = useClosable(
		closable,
		closeIcon,
		customCloseIconRender,
		undefined,
		true
	);
	const headerNode = React.useMemo( () => {
		if ( ! title && ! mergedClosable ) {
			return null;
		}
		return (
			<div
				style={ headerStyle }
				className={ classNames( `${ prefixCls }-header`, {
					[ `${ prefixCls }-header-close-only` ]: mergedClosable && ! title && ! extra,
				} ) }
			>
				<div className={ `${ prefixCls }-header-title` }>
					{ mergedCloseIcon }
					{ title && <div className={ `${ prefixCls }-title` }>{ title }</div> }
				</div>
				{ extra && <div className={ `${ prefixCls }-extra` }>{ extra }</div> }
			</div>
		);
	}, [ mergedClosable, mergedCloseIcon, extra, headerStyle, prefixCls, title ] );
	const footerNode = React.useMemo( () => {
		if ( ! footer ) {
			return null;
		}
		const footerClassName = `${ prefixCls }-footer`;
		return (
			<div className={ footerClassName } style={ footerStyle }>
				{ footer }
			</div>
		);
	}, [ footer, footerStyle, prefixCls ] );
	return (
		<div className={ `${ prefixCls }-wrapper-body` } style={ drawerStyle }>
			{ headerNode }
			<div className={ `${ prefixCls }-body` } style={ bodyStyle }>
				{ children }
			</div>
			{ footerNode }
		</div>
	);
};
export default DrawerPanel;
