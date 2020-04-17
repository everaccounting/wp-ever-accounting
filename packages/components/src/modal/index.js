/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
/**
 * WordPress dependencies
 */
import { Modal as BaseElement } from '@wordpress/components';
import PropTypes from 'prop-types';
import classNames from 'classnames';

export default class Modal extends Component {
	static propTypes = {
		className: PropTypes.string,
		title: PropTypes.string,
		onClose: PropTypes.func,
		shouldCloseOnClickOutside: PropTypes.bool,
		width: PropTypes.string,
		overlayClassName: PropTypes.string,
	};

	static defaultProps = {
		shouldCloseOnClickOutside: false,
	};

	render() {
		const classes = classNames('ea-modal');
		const {onClose} = this.props;
		return (
			<Fragment>
				<BaseElement {...this.props} className={classes} overlayClassName="ea-modal-overlay" onRequestClose={onClose}>
					{this.props.children}
				</BaseElement>
			</Fragment>
		);
	}
}
