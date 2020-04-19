import {Component, Fragment} from 'react';
import PropTypes from "prop-types";
import Gridicon from "gridicons";
import classNames from 'classnames';
import Card from "../card";

export default class EmptyContent extends Component {
	static propTypes = {
		className: PropTypes.string,
		icon: PropTypes.string,
		iconSize: PropTypes.number,
		title: PropTypes.string,
		subtitle: PropTypes.string,
	};

	static defaultProps = {
		className: '',
		icon: 'bug',
		iconSize: 150,
	};

	render() {
		const {className, icon, iconSize, title, subtitle} = this.props;
		return (
			<Fragment>
				<Card className={classNames('ea-empty-content', className)}>
					<div className="ea-row">
						{icon && iconSize && <div className="ea-col ea-empty-content__left"><Gridicon icon={icon} size={iconSize}/></div>}
						<div className="ea-col ea-empty-content__right">
							{title && <h2 className="ea-empty-content__title">{title}</h2>}
							{subtitle && <h2 className="ea-empty-content__subtitle">{subtitle}</h2>}
							{this.props.children && this.props.children}
						</div>
					</div>
				</Card>
			</Fragment>
		)
	}
}
