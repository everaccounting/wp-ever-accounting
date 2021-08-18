/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { speak } from '@wordpress/a11y';
import { useEffect, forwardRef, renderToString } from '@wordpress/element';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import { noop } from 'lodash';
/**
 * Internal dependencies
 */

const NOTICE_TIMEOUT = 3000;

/**
 * Custom hook which announces the message with the given politeness, if a
 * valid message is provided.
 *
 * @param {string|WPElement}     [message]  Message to announce.
 * @param {'polite'|'assertive'} politeness Politeness to announce.
 */
function useSpokenMessage(message, politeness) {
	const spokenMessage =
		typeof message === 'string' ? message : renderToString(message);

	useEffect(() => {
		if (spokenMessage) {
			speak(spokenMessage, politeness);
		}
	}, [spokenMessage, politeness]);
}

function Snackbar(
	{
		className,
		children,
		spokenMessage = children,
		politeness = 'polite',
		onRemove = noop,
		icon = null,
		explicitDismiss = false,
		onDismiss = noop,
		status = 'info',
	},
	ref
) {
	onDismiss = onDismiss || noop;

	function dismissMe(event) {
		if (event && event.preventDefault) {
			event.preventDefault();
		}

		onDismiss();
		onRemove();
	}

	useSpokenMessage(spokenMessage, politeness);

	// Only set up the timeout dismiss if we're not explicitly dismissing.
	useEffect(() => {
		const timeoutHandle = setTimeout(() => {
			if (!explicitDismiss) {
				onDismiss();
				onRemove();
			}
		}, NOTICE_TIMEOUT);

		return () => clearTimeout(timeoutHandle);
	}, [onDismiss, onRemove]);

	const classes = classnames(
		className,
		'ea-snackbar__notice',
		`notice-${status}`,
		{
			'ea-snackbar__explicit-dismiss': !!explicitDismiss,
			'ea-snackbar__content-with-icon': !!icon,
		}
	);

	return (
		// eslint-disable-next-line jsx-a11y/no-static-element-interactions
		<div
			ref={ref}
			className={classes}
			onClick={!explicitDismiss ? dismissMe : noop}
			tabIndex="0"
			onKeyPress={!explicitDismiss ? dismissMe : noop}
			aria-label={!explicitDismiss ? __('Dismiss this notice') : ''}
		>
			{icon && <div className="ea-snackbar__icon">{icon}</div>}
			<div>{children}</div>
		</div>
	);
}

export default forwardRef(Snackbar);
