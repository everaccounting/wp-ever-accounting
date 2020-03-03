import React from 'react'
import { Route, Switch, Redirect } from 'react-router'
import Revenues from "../page/revenues";
import EditRevenue from "../component/edit-revenue";
const routes = (
	<Switch>
		<Route exact path="/new" component={EditRevenue} />
		<Route exact path="/:id(\d+)" component={EditRevenue} />
		<Route exact path="/" component={Revenues} />
		<Redirect from="*" to="/dashbord" />
	</Switch>
);

export default routes
