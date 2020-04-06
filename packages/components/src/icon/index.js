/**
 * External dependencies
 */
import { Component } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
export default class Icon extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { icon, ...rest } = this.props;
		return <i className={classNames('fa', 'ea-icon', `fa-${icon}`)} {...rest} />;
	}
}
