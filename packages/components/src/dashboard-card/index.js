import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import CompactCard from "../compact-card";
import Card from "../card";


export default class DashboardCard extends Component{
	constructor(props) {
		super(props);
	}

	render() {
		const {title, tools, className} = this.props;
		const classes = classNames('ea-dashboard-card', className);
		return(
			<div className={classes}>
				{title && <CompactCard className="ea-dashboard-card__header">
					<span className="ea-dashboard-card__title">{title}</span>
					{tools && <span className="ea-dashboard-card__tools">{tools}</span>}
				</CompactCard>
				}
				<Card className="ea-dashboard-card__body">
					{this.props.children && this.props.children}
				</Card>
			</div>
		)
	}
}
