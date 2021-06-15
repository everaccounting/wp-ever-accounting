/**
 * WordPress dependencies
 */
import { Button, Spinner } from '@wordpress/components';
import { doAction } from '@wordpress/hooks';
/**
 * Internal dependencies
 */
import { getTabs } from './controller';
/**
 * External dependencies
 */
import { Redirect } from '@eaccounting/navigation';
import { createElement, Suspense } from '@wordpress/element';
import classnames from 'classnames';
import { useUser } from '@eaccounting/data';
export default function Banking( props ) {
	const { currentUserCan } = useUser();
	const tabs = getTabs().filter(
		( tab ) => ! tab.capability || currentUserCan( tab.capability )
	);

	const getCurrentTab = () => {
		const { tab } = props.query;
		const currentTab = tabs.find( ( s ) => s.key === tab );

		if ( ! currentTab ) {
			return tabs[ 0 ];
		}
		return currentTab;
	};

	const onClickTab = ( tab ) => {
		return Redirect( { tab: tab.key } );
	};

	const currentTab = getCurrentTab();
	const container = createElement( currentTab.container, {
		...props,
		currentTab,
	} );

	const classes = classnames(
		'eaccounting-tabbed-area',
		`tab-${ currentTab.key }`
	);

	return (
		<div className={ classes }>
			<nav className="nav-tab-wrapper ea-nav-tab-wrapper">
				{ tabs.map( ( tab ) => {
					const buttonClass = classnames( 'nav-tab', {
						'nav-tab-active': tab.key === currentTab.key,
					} );
					return (
						<Button
							key={ tab.key }
							className={ buttonClass }
							onClick={ () => onClickTab( tab ) }
							isDefault={ true }
						>
							{ tab.label }
						</Button>
					);
				} ) }
			</nav>
			{ doAction(
				`eaccounting_banking_before_tab_${ currentTab.key }`,
				props
			) }
			<Suspense fallback={ <Spinner /> }>{ container }</Suspense>
			{ doAction(
				`eaccounting_banking_tab_after_${ currentTab.key }`,
				props
			) }
		</div>
	);
}
