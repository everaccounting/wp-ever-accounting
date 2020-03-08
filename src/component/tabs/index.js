import { Fragment } from 'react';
import { NavLink } from 'react-router-dom';
import './style.scss';

export const Tabs = props => {
	const { tabs } = props;

	return (
		<Fragment>
			<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
				{tabs.map((tab, index) => {
					return (
						<NavLink
							key={index}
							className="nav-tab"
							activeClassName="nav-tab-active"
							to={tab.path}
							isActive={(match, location) => location.pathname.includes(tab.path)}
						>
							{tab.name}
						</NavLink>
					);
				})}
			</nav>
			<div className="clearfix" />
		</Fragment>
	);
};

export default Tabs;
