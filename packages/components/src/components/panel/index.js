/**
 * WordPress dependencies
 */
import { NavigableMenu, MenuItem } from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * Internal dependencies
 */
import Body from './body';
import './style.scss';

function Panel( props ) {
	const { className, title, actions, tabList, tabActiveKey, children, footer, ...others } = props;
	const onTabChange = ( key ) => {
		props.onTabChange?.( key );
	};

	const tabs = tabList && tabList.length > 0 && (
		<NavigableMenu className="eac-panel__tabs" orientation="horizontal">
			{ tabList.map( ( tab ) => (
				<MenuItem
					key={ tab.key }
					onClick={ () => onTabChange( tab.key ) }
					className={ tabActiveKey === tab.key ? 'is-active' : '' }
				>
					{ tab.label }
				</MenuItem>
			) ) }
		</NavigableMenu>
	);

	const renderHeader = () => {
		if ( ! title && ! actions && ! tabs ) {
			return null;
		}
		return (
			<div className="eac-panel__header">
				{ tabs }
				{ title && <div className="eac-panel__title">{ title }</div> }
				{ actions && <div className="eac-panel__actions">{ actions }</div> }
			</div>
		);
	};

	const renderFooter = () => {
		if ( ! footer ) {
			return null;
		}
		return <div className="eac-panel__footer">{ footer }</div>;
	};

	const renderChildren = () => {
		// if child is array, render as it is otherwise wrap it in Body component.
		if ( Array.isArray( children ) ) {
			return children;
		}

		// check if the child is a node, if so, render it as it is.
		if ( typeof children === 'object' ) {
			return children;
		}

		return <Body>{ children }</Body>;
	};

	const classes = classnames( 'eac-panel', className );

	return (
		<div className={ classes } { ...others }>
			{ renderHeader() }
			{ renderChildren() }
			{ renderFooter() }
		</div>
	);
}
Panel.Body = Body;
export default Panel;
