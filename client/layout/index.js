/**
 * WordPress dependencies
 */
import { SlotFillProvider } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
/**
 * External dependencies
 */
import { Router, Route, Switch, Redirect } from 'react-router-dom';
import { parse } from 'qs';
import { getHistory } from '@eaccounting/navigation';
/**
 * Internal dependencies
 */
import { Controller, getPages } from './controller';
import { useUser } from '@eaccounting/data';
import { SnackbarList } from '@eaccounting/components';

const getQuery = ( searchString ) => {
	if ( ! searchString ) {
		return {};
	}

	const search = searchString.substring( 1 );
	return parse( search );
};

export const Layout = ( props ) => {
	const notices = useSelect( ( select ) =>
		select( 'core/notices' ).getNotices()
	);
	console.log( notices );
	const { location } = props;
	const query = getQuery( location && location.search );
	return (
		<SlotFillProvider>
			<div className="eaccounting-layout">
				<Controller { ...props } query={ query } />
				<SnackbarList notices={ notices } />
			</div>
		</SlotFillProvider>
	);
};

export const PageLayout = ( entryProps ) => {
	const { currentUserCan } = useUser();
	return (
		<>
			<Router history={ getHistory() }>
				<Switch>
					{ getPages()
						.filter(
							( page ) =>
								! page.capability ||
								currentUserCan( page.capability )
						)
						.map( ( page ) => {
							return (
								<Route
									key={ page.path }
									path={ page.path }
									exact
									render={ ( props ) => (
										<Layout
											page={ page }
											{ ...props }
											{ ...entryProps }
										/>
									) }
								/>
							);
						} ) }
					<Redirect from={ '*' } to={ '/setup' } />
				</Switch>
			</Router>
		</>
	);
};
