import React from 'react';
import {translate as __} from 'lib/locale';
import PropTypes from 'prop-types';
import Link from "../link";
import classNames from 'classnames';
import {connect} from "react-redux";


const Tabs = (props) => {
	const {tabs, pathname} = props;

	const classes = (path) => {
		return classNames('nav-tab', {
			'nav-tab-active':pathname.includes(path)
		});
	};

	return (
		<nav className="nav-tab-wrapper eaccounting-nav-tab-wrapper">
			{tabs.map((tab, index) => {
				return <Link key={index} className={classes(tab.path)} href={tab.path}>{tab.name}</Link>
			})}
		</nav>
	)
};
const mapStateToProps = state => ({
	pathname: state.router.location.pathname,
});

export default connect(mapStateToProps)(Tabs)
