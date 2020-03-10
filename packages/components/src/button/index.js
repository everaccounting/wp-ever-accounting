/** @format */
/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { Button as BaseElement } from '@wordpress/components';
/**
 * Style dependencies
 */

export default class Button extends Component {
	static propTypes = {
		compact: PropTypes.bool,
		primary: PropTypes.bool,
		busy: PropTypes.bool,
		borderless: PropTypes.bool,
	};

	static defaultProps = {
		type: 'button',
	};

	render() {
		const className = classNames('ecrm-button', this.props.className, {
			'is-compact': this.props.compact,
			'is-borderless': this.props.borderless,
		});

		const { compact, primary, scary, busy, borderless, target, rel, ...props } = this.props;

		return <BaseElement {...props} className={className} />;
	}
}
