/**
 * External dependencies
 */
import classNames from 'classnames';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import './style.scss';

function SectionHeader(props) {
	const { children, menu, title, actions, isCard, ...restProps } = props;
	const classes = classNames('eac-section-header', props.className, {
		'is--card': isCard,
	});

	return (
		<div className={classes} {...restProps}>
			<div className="eac-section-header__title">
				{title && <h2>{title}</h2>}
				{children}
			</div>
			{actions && <div className="eac-section-header__actions">{actions}</div>}

			{menu && <div className="eac-section-header__menu">{menu}</div>}
		</div>
	);
}

SectionHeader.propTypes = {
	/**
	 * Additional CSS classes.
	 */
	className: PropTypes.string,
	/**
	 * An optional menu to display in the header.
	 */
	menu: PropTypes.node,
	/**
	 * The title to use for this card.
	 */
	title: PropTypes.oneOfType([PropTypes.string, PropTypes.node]).isRequired,
};

export default SectionHeader;
