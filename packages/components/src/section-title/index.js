/**
 * External dependencies
 */
import classNames from 'classnames';
import { Component } from 'react';
import PropTypes from 'prop-types';

export default class SectionTitle extends Component {
	static propTypes = {
		title: PropTypes.string.isRequired,
	};

	static defaultProps = {
		title: '',
	};

	render() {
		const { title, className } = this.props;
		const classes = classNames(className, 'ea-section-title');

		return (
			<div className={classes}>
				{title && <h1 className="ea-section-title__title">{title}</h1>}
				{this.props.children && <div className="ea-section-title__children">{this.props.children}</div>}
			</div>
		);
	}
}
