/**
 * External dependencies
 */
import { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Card from '../card';
import Gridicon from 'gridicons';

export default class ActionPanel extends Component {
	static propTypes = {
		className: PropTypes.string,
		primary: PropTypes.bool,
		icon: PropTypes.string,
		iconSize: PropTypes.number,
		title: PropTypes.string,
	};

	static defaultProps = {
		className: '',
		primary: true,
		icon: 'bug',
		iconSize: 150,
	};

	render() {
		const { primary, icon, iconSize, title, className } = this.props;
		const classes = classNames(['ea-action-panel', className, primary ? 'is-primary' : '']);
		return (
			<Card className={classes}>
				<div className="ea-action-panel__figure">{icon && iconSize && <Gridicon icon={icon} size={iconSize} />}</div>
				<div className="ea-action-panel__body">
					{title && <h2 className="ea-action-panel__title">{title}</h2>}
					{this.props.children}
				</div>
			</Card>
		);
	}
}
