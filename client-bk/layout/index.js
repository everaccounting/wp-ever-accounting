/**
 * WordPress dependencies
 */
import { SlotFillProvider, Button } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';
import { Component, lazy, Suspense } from '@wordpress/element';
/**
 * External dependencies
 */
import { Router, Route, Switch, NavLink } from 'react-router-dom';
import PropTypes from 'prop-types';
import { get, isFunction, identity } from 'lodash';
import { parse } from 'qs';
import { getHistory, updateQueryString } from '@eaccounting/navigation';
/**
 * Internal dependencies
 */
import { Controller, getPages } from './controller';

const getQuery = (searchString) => {
	if (!searchString) {
		return {};
	}

	const search = searchString.substring(1);
	return parse(search);
};

export const Layout = (props) => {
	const { location } = props;
	const query = getQuery(location && location.search);
	return (
		<SlotFillProvider>
			<div className="eaccounting-layout">
				{/*{getPages().map((page) => {*/}
				{/*	return (*/}
				{/*		<>*/}
				{/*			<Button*/}
				{/*				key={page.path}*/}
				{/*				onClick={() => updateQueryString({}, page.path)}*/}
				{/*			>*/}
				{/*				page*/}
				{/*			</Button>*/}
				{/*		</>*/}
				{/*	);*/}
				{/*})}*/}
				<Controller {...props} query={query} />
			</div>
		</SlotFillProvider>
	);
};

export const PageLayout = () => {
	return (
		<>
			<Router history={getHistory()}>
				<Switch>
					{getPages()
						// .filter(
						// 	(page) =>
						// 		!page.capability || currentUserCan(page.capability)
						// )
						.map((page) => {
							return (
								<Route
									key={page.path}
									path={page.path}
									exact
									render={(props) => (
										<Layout page={page} {...props} />
									)}
								/>
							);
						})}
				</Switch>
			</Router>
		</>
	);
};
