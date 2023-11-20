/**
 * External dependencies
 */
import classnames from 'classnames';
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { NavigableMenu, MenuItem, Spinner } from '@wordpress/components';
import { useState, Suspense } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import './style.scss';

function Card( props ) {
	const {
		className,
		title,
		actions,
		tabs = [],
		activeTab,
		children,
		footer,
		onTabChange,
		size = 'small',
		...others
	} = props;

	const hasTabs = tabs && tabs.length > 0;
	const selectedTab = tabs.find( ( tab ) => tab?.key === activeTab && ! tab?.disabled );
	const [ activeTabKey, setActiveTabKey ] = useState( () => selectedTab?.key || tabs[ 0 ]?.key );

	const onTabChangeState = ( nextTab ) => {
		setActiveTabKey( nextTab );
		onTabChange?.( nextTab );
	};

	const classes = classnames( 'eac-card', className, {
		'eac-card--loading': !! props?.loading,
		'eac-card--bordered': !! props?.bordered,
		'eac-card--tabbed': tabs && tabs.length > 0,
		[ `eac-card--${ size }` ]: size,
	} );

	const tabContent = tabs.find( ( tab ) => tab?.key === activeTabKey )?.content;

	return (
		<div className={ classes } { ...others }>
			{ ( title || actions ) && (
				<div className="eac-card__header">
					{ title && <div className="eac-card__title">{ title }</div> }
					{ actions && <div className="eac-card__actions">{ actions }</div> }
				</div>
			) }
			{ hasTabs && (
				<NavigableMenu
					className="eac-card__tabs"
					orientation="horizontal"
					role="tablist"
					aria-label={ __( 'Tabs' ) }
					aria-orientation="horizontal"
				>
					{ tabs.map( ( tab ) => (
						<MenuItem
							key={ tab.key }
							className={ classnames( 'eac-card__tabs-menu-item', {
								'is--active': tab.key === activeTabKey,
								'is--disabled': tab.disabled,
							} ) }
							onClick={ () => {
								if ( tab.disabled ) {
									return;
								}
								onTabChangeState( tab.key );
							} }
							role="tab"
							aria-selected={ tab.key === activeTabKey }
							disabled={ tab.disabled }
							variant="link"
						>
							{ tab.label }
						</MenuItem>
					) ) }
				</NavigableMenu>
			) }
			{ ( children || tabContent ) && (
				<div className="eac-card__body">
					{ typeof children === 'function' ? children( activeTabKey ) : children }
					{ hasTabs && tabContent && (
						<Suspense fallback={ <Spinner /> }>{ tabContent }</Suspense>
					) }
				</div>
			) }
			{ footer && <div className="eac-card__footer">{ footer }</div> }
		</div>
	);
}

Card.propTypes = {
	className: PropTypes.string,
	title: PropTypes.oneOfType( [ PropTypes.string, PropTypes.node ] ),
	actions: PropTypes.oneOfType( [ PropTypes.string, PropTypes.node ] ),
	tabs: PropTypes.arrayOf(
		PropTypes.shape( {
			key: PropTypes.string.isRequired,
			label: PropTypes.string.isRequired,
			disabled: PropTypes.bool,
		} )
	),
	activeTab: PropTypes.string,
	children: PropTypes.oneOfType( [ PropTypes.func, PropTypes.node ] ),
	footer: PropTypes.oneOfType( [ PropTypes.string, PropTypes.node ] ),
};

export default Card;
