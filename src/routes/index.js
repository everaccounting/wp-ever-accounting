import React from 'react'
import {Route, Switch, Redirect} from 'react-router'
import Revenues from "../page/revenues";
import EditRevenue from "../component/edit-revenue";
import Incomes from "../page/incomes";

const routes = (
	<Switch>
		//income
		<Route exact path="/incomes/:tab/new" component={Incomes}/>
		<Route exact path="/incomes/:tab/:id(\d+)" component={Incomes}/>
		<Route exact path="/incomes/:tab" component={Incomes}/>
		<Redirect from="/incomes" to="/incomes/revenues"/>

		<Redirect from="*" to="/dashbord"/>
	</Switch>
);

export default routes
