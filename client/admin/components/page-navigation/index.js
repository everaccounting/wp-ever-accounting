/**
 * External dependencies
 */
import { Link, useLocation } from 'react-router-dom';
/**
 * Internal dependencies
 */
import './style.scss';

function PageNavigation( props ) {
	const { routes } = props;
	const pathname = useLocation().pathname;
	const tabs = routes?.children?.filter( ( route ) => route.tab );
	const isCurrentRoute = ( tab ) => {
		const path = tab.path.replace( /[^a-zA-Z0-9#\/]/g, '' ).replace( /\/$/, '' );
		console.log( `${ routes.path }/${ path }` );
		return pathname.includes( `${ routes.path }/${ path }` );
	};
	return (
		<>
			{ tabs && tabs.length > 1 && (
				<nav className="nav-tab-wrapper eac-nav">
					{ tabs.map( ( tab ) => (
						<Link
							key={ tab.name }
							to={ tab.path.replace( /[^a-zA-Z0-9#\/]/g, '' ).replace( /\/$/, '' ) }
							className={ `nav-tab ${ isCurrentRoute( tab ) ? 'nav-tab-active' : '' }` }
						>
							{ tab.name }
						</Link>
					) ) }
				</nav>
			) }
		</>
	);
}

export default PageNavigation;
