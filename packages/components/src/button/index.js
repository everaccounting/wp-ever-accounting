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
		const { compact, primary, secondary, scary, busy, borderless, target, rel, ...props } = this.props;
		const className = classNames('ea-button', this.props.className, {
			'is-compact': this.props.compact,
			'button-secondary': this.props.secondary,
			'button-primary': this.props.primary,
			'is-borderless': this.props.borderless,
		});

		return <BaseElement {...props} className={className} />;
	}
}
