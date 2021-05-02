/**
 * External dependencies
 */
import { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Card from '../card';
import BackButton from '../back-button';
/**
 * WordPress dependencies
 */
import { Dashicon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default class FormCard extends Component {
	static propTypes = {
		title: PropTypes.string.isRequired,
		className: PropTypes.string,
		backButton: PropTypes.bool,
	};

	constructor(props) {
		super(props);
	}

	render() {
		const { title, className } = this.props;
		return (
			<div className={classNames('ea-form-card', className)}>
				<CompactCard className="ea-form-card__header">
					{title && <h3 className="ea-form-card__header-title">{title}</h3>}
					<BackButton compact>
						<Dashicon icon="arrow-left-alt" />
						{__('Back')}
					</BackButton>
				</CompactCard>
				<Card compact>{this.props.children && this.props.children}</Card>
			</div>
		);
	}
}
