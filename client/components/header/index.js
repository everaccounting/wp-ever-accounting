import { Component, Fragment } from 'react';
import {__} from '@wordpress/i18n';
import {Dashicon} from "@wordpress/components";
import './style.scss';
import { NavLink } from 'react-router-dom';

const navigations = [
	{
		name:__('Dashboard'),
		path:'/'
	},
	// {
	// 	name:__('Items'),
	// 	path:'/items'
	// },
	{
		name:__('Sales'),
		path:'/sales'
	},
	{
		name:__('Purchases'),
		path:'/purchases'
	},
	{
		name:__('Banking'),
		path:'/banking'
	},
	{
		name:__('Settings'),
		path:'/settings'
	}
];



export default class Header extends Component {
	constructor(props) {
		super(props);
		this.isActive = this.isActive.bind(this);
	}

	isActive(location, path){
		const pathname = location.pathname.split('/')[1];
		const menuName = path.split('/')[1];
		return pathname === menuName;
	}

	render() {
		return (
			<div className="eaccounting-header">
				<div className="eaccounting-header__left">
					<Dashicon icon="chart-area" size={22} className="eaccounting-logo"/>
					<span className="eaccounting-title">EAccounting</span>
				</div>
				<div className="eaccounting-header__right">
					<nav className="eaccounting-navigation">
						{navigations.map((tab, index) => {
							return (
								<NavLink
									key={index}
									exact
									className="eaccounting-navigation-item"
									activeClassName="active"
									to={tab.path}
									isActive={(match, location)=> this.isActive(location, tab.path)}>
									{tab.name}
								</NavLink>
							);
						})}

						<a href="#" className="eaccounting-navigation-item">Support</a>
					</nav>
				</div>
			</div>
		);
	}
}
