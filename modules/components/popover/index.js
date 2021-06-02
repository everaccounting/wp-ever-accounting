/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import { useEffect, createPortal } from '@wordpress/element';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import PopoverContainer from './container';
import ClickOutside from '../click-outside';
import { getPosition } from './dimensions';
import { getPortal } from '../lib';
import './style.scss';

/**
 * Render callback.
 *
 * @callback contentRender
 * @param {toggleCallback} toggle
 */

/**
 * Toggle callback.
 *
 * @callback toggleCallback
 */

/**
 * Render callback.
 *
 * @callback toggleRender
 * @param {boolean} isShowing Is the menu currently visible?
 * @param {toggleCallback} toggle Toggle the dropdown on/off.
 */

/**
 * Displays a dropdown - a toggle that when clicked shows a dropdown area.
 *
 * @param {Object} props - Component props.
 * @param {string} [props.className] - Additional class name.
 * @param {('left'|'right'|'centre')} [props.align='left'] - Align the popover on the `left` or `right`.
 * @param {boolean} [props.hasArrow=false] - Show a small arrow pointing at the toggle when the popover is shown.
 * @param {toggleCallback} [props.onClose] - Callback when the popover is closed.
 * @param {contentRender} props.children - Called when the popover should be shown
 * @param {} props.popoverPosition - Position where the popover should be shown
 * @param {Object | null} [props.style=null] - Additional style params
 */
function Popover(props) {
	const {
		children,
		className,
		align = 'left',
		onClose,
		hasArrow = false,
		popoverPosition = null,
		style = null,
	} = props;

	/**
	 * Hide the dropdown
	 *
	 * @param {Event} ev - Event
	 */
	function onOutside(ev) {
		if (
			ClickOutside.isOutside(ev, popoverPosition.ref) === false &&
			ev.key !== 'Escape'
		) {
			return;
		}

		onClose();
	}

	// Close popover when window resized
	useEffect(() => {
		window.addEventListener('resize', onClose);

		return () => {
			window.removeEventListener('resize', onClose);
		};
	}, []);

	return createPortal(
		<ClickOutside
			className={classnames('ea-popover', className)}
			onOutside={onOutside}
		>
			<PopoverContainer
				position={getPosition(popoverPosition)}
				popoverPosition={popoverPosition}
				align={align}
				hasArrow={hasArrow}
				style={style}
			>
				{children}
			</PopoverContainer>
		</ClickOutside>,
		getPortal('ea-dropdown-portal')
	);
}

/**
 * Get the dimensions of the node.
 *
 * @param {HTMLElement|null} ref - The dom node.
 */
export function getPopoverPosition(ref) {
	const parentNode = document.getElementById('wpwrap');
	if (ref === null || parentNode === null) {
		return {};
	}

	const parentRect = parentNode.getBoundingClientRect();
	const { height, width, left, top } = ref.getBoundingClientRect();

	return {
		left: left - parentRect.left,
		top: top - parentRect.top + 1,
		width,
		height,
		parentWidth: parentRect.width,
		parentHeight: parentRect.height,
		ref,
	};
}

export default Popover;
