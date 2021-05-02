/**
 * External dependencies
 */
import React, { PureComponent } from 'react';
import classNames from 'classnames';
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import './style.scss';
/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/components';

const getClassName = ({
	className,
	compact,
	displayAsLink,
	highlight,
	href,
	onClick,
}) =>
	classNames(
		'ea-card',
		className,
		{
			'is-card-link': displayAsLink || href,
			'is-clickable': onClick,
			'is-compact': compact,
			'is-highlight': highlight,
		},
		highlight ? 'is-' + highlight : false
	);

class Card extends PureComponent {
	static propTypes = {
		className: PropTypes.string,
		displayAsLink: PropTypes.bool,
		href: PropTypes.string,
		tagName: PropTypes.elementType,
		target: PropTypes.string,
		compact: PropTypes.bool,
		highlight: PropTypes.oneOf(['error', 'info', 'success', 'warning']),
	};

	render() {
		const {
			children,
			compact,
			displayAsLink,
			highlight,
			tagName: TagName = 'div',
			href,
			target,
			...props
		} = this.props;

		return href ? (
			<a
				{...props}
				href={href}
				target={target}
				className={getClassName(this.props)}
			>
				<Icon
					className="ea-card__link-indicator"
					icon={target ? 'external' : 'arrow-right-alt2'}
				/>
				{children}
			</a>
		) : (
			<TagName {...props} className={getClassName(this.props)}>
				{displayAsLink && (
					<Icon
						className="ea-card__link-indicator"
						icon={target ? 'external' : 'arrow-right-alt2'}
					/>
				)}
				{children}
			</TagName>
		);
	}
}

export default Card;
