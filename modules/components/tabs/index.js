/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { createElement, Suspense } from '@wordpress/element';
import { generatePath, getPath } from '@eaccounting/navigation';
import { useUser } from '@eaccounting/data';

/**
 * Internal dependencies
 */
import Link from '../link';
import Loading from '../loading';
import './style.scss';

export default function Tabs( props ) {
	const { tabs: components, onClick = ( x ) => x } = props;
	const { currentUserCan } = useUser();
	const tabs = components.filter(
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
		onClick( tab );
	};

	const currentTab = getCurrentTab();
	const container = createElement( currentTab.container, {
		...props,
		currentTab,
	} );
	const classes = classnames( 'eaccounting-tabs', `tab-${ currentTab.key }` );

	return (
		<div className={ classes }>
			<nav className="nav-tab-wrapper eaccounting-tabs__nav-wrapper">
				{ tabs.map( ( tab ) => {
					const buttonClass = classnames(
						'eaccounting-tabs__nav',
						'nav-tab',
						{
							'is--active nav-tab-active':
								tab.key === currentTab.key,
						}
					);
					return (
						<Link
							key={ tab.key }
							className={ buttonClass }
							href={ generatePath(
								{ tab: tab.key },
								getPath(),
								{}
							) }
							type="eaccounting"
							onClick={ () => onClickTab( tab ) }
						>
							{ tab.label }
						</Link>
					);
				} ) }
			</nav>
			<Suspense fallback={ <Loading /> }>{ container }</Suspense>
		</div>
	);
}
