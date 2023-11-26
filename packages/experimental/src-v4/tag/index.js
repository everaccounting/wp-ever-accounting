/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { Fragment, useState } from '@wordpress/element';
import { Button, Popover } from '@wordpress/components';
import { Icon, closeSmall } from '@wordpress/icons';
import { decodeEntities } from '@wordpress/html-entities';
import { withInstanceId } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import './style.scss';

const Tag = ({ id, instanceId, label, popoverContents, remove, screenReaderLabel, className }) => {
	const [isVisible, setIsVisible] = useState(false);
	screenReaderLabel = screenReaderLabel || label;
	if (!label) {
		// A null label probably means something went wrong
		// @todo Maybe this should be a loading indicator?
		return null;
	}
	label = decodeEntities(label);
	const classes = classnames('eac-tag', className, {
		'has-remove': !!remove,
	});
	const labelId = `eac-tag__label-${instanceId}`;
	const labelTextNode = (
		<Fragment>
			<span className="screen-reader-text">{screenReaderLabel}</span>
			<span aria-hidden="true">{label}</span>
		</Fragment>
	);
	return (
		<span className={classes}>
			{popoverContents ? (
				<Button className="eac-tag__text" id={labelId} onClick={() => setIsVisible(true)}>
					{labelTextNode}
				</Button>
			) : (
				<span className="eac-tag__text" id={labelId}>
					{labelTextNode}
				</span>
			)}
			{popoverContents && isVisible && (
				<Popover onClose={() => setIsVisible(false)}>{popoverContents}</Popover>
			)}
			{remove && (
				<Button
					className="eac-tag__remove"
					onClick={remove(id)}
					// translators: %s is the name of the tag being removed.
					label={sprintf(__('Remove %s', 'wp-ver-accounting'), label)}
					aria-describedby={labelId}
				>
					<Icon icon={closeSmall} size={20} className="clear-icon" />
				</Button>
			)}
		</span>
	);
};
export default withInstanceId(Tag);
