/**
 * External dependencies
 */
import React, {PureComponent} from 'react';
import PropTypes from 'prop-types';
import classnames from 'classnames';
import {BaseControl, Modal as BaseElement} from '@wordpress/components';

export default class FormTextInput extends PureComponent {
	static propTypes = {
		className: PropTypes.string,
		icon: PropTypes.string
	};

	static defaultProps = {
		icon: '',
	};

	render() {
		const {className} = this.props;
		return (
			<BaseControl {...this.props} className={classnames('ea-form-group', className)}>
				<div className="ea-input-group">
					<div className="ea-input-group-addon"><i className="fa fa-id-card-o"/></div>
					{this.props.children}
				</div>
			</BaseControl>
		);
	}
}

