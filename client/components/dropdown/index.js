import {useState, useRef} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import Popover, { getPopoverPosition } from '../popover';
import './style.scss';
import classnames from "classnames";

function Dropdown( props ) {
	const {renderContent, className, renderToggle, align = 'left', hasArrow = false, matchMinimum = false, disabled = false} = props;
	const [isShowing, setShowing] = useState(false);
	const [togglePosition, setTogglePosition] = useState(null);
	const toggleRef = useRef(null);

	/**
	 * Toggle the dropdown
	 * @param {Event} ev - Event
	 */
	const toggleDropdown = ( ev ) => {
		const position = getPopoverPosition( toggleRef.current );

		ev && ev.stopPropagation();

		if ( ! disabled ) {
			setTogglePosition( position );
			setShowing( ! isShowing );
		}
	};

	return (
		<>
			<div className={ classnames( 'ea-popover__toggle', className, disabled && 'ea-popover__toggle__disabled' ) } ref={ toggleRef }>
				{ renderToggle( isShowing, toggleDropdown ) }
			</div>

			{ isShowing && (
				<Popover
					align={ align }
					hasArrow={ hasArrow }
					className={ className }
					onClose={ () => setShowing( false ) }
					popoverPosition={ togglePosition }
					style={ matchMinimum ? { minWidth: togglePosition.width + 'px' } : null }
				>
					{ renderContent( () => setShowing( false ) ) }
				</Popover>
			) }
		</>
	);
}

export default Dropdown;
