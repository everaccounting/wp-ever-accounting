/**
 * External dependencies
 */
import { Redirect, Route, Router, Switch } from 'react-router-dom';
import { parse } from 'qs';

/**
 * WordPress dependencies
 */
import { SlotFillProvider, Popover, Slot } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
/**
 * Internal dependencies
 */
import { Controller, getPages } from './controller';
import { useUser } from '@eaccounting/data';
import { generatePath, getHistory } from '@eaccounting/navigation';
import { SnackbarList } from '@eaccounting/components';
// import Header from './header';
import './style.scss';

const getQuery = ( searchString ) => {
	if ( ! searchString ) {
		return {};
	}

	const search = searchString.substring( 1 );
	return parse( search );
};

const PanelSlot = () => {
	return (
		<Slot name="panel">
			{ ( fills ) => {
				return fills.length > 0 ? (
					<div className="woocommerce-layout__activity-panel-wrapper is-open">
						{ fills }
					</div>
				) : (
					<div className="woocommerce-layout__activity-panel-wrapper">
						{ fills }
					</div>
				);
			} }
		</Slot>
	);
};

export const Layout = ( props ) => {
	const { location, isEmbedded = false } = props;
	// eslint-disable-next-line no-unused-vars
	const { removeNotice } = useDispatch( 'core/notices' );
	// eslint-disable-next-line no-unused-vars
	const notices = useSelect( ( select ) =>
		select( 'core/notices' ).getNotices()
	);
	const query = getQuery( location && location.search );
	return (
		<SlotFillProvider>
			<div className="eaccounting-layout">
				{ /*<Header isEmbedded={ isEmbedded } query={ query } />*/ }
				{ ! isEmbedded && (
					<div className="eaccounting-layout__main">
						<Controller { ...props } query={ query } />
					</div>
				) }
				{ notices && (
					<SnackbarList
						notices={ notices }
						onRemove={ removeNotice }
					/>
				) }
			</div>
			<Popover.Slot />
			<PanelSlot />
		</SlotFillProvider>
	);
};

export const EmbeddedApp = () => <Layout isEmbedded />;

export function App() {
	// eslint-disable-next-line no-unused-vars
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
										<Layout page={ page } { ...props } />
									) }
								/>
							);
						} ) }

					<Redirect to={ generatePath( {}, '/sales' ) } />
				</Switch>
			</Router>
		</>
	);
}
