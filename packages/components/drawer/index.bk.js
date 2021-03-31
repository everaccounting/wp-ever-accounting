import {useState, useEffect, createPortal, useRef} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import ClickOutside from "../click-outside";
import {getPortal} from "../lib";
import {CSSTransition} from 'react-transition-group';
import {Button} from "@wordpress/components";

import './style.scss';

function Drawer(props) {
	const {children, className, onClose} = props;
	const [height, setHeight] = useState(0);
	const ref = useRef(null)

	useEffect(() => {
		const headerHeight = ref.current.querySelector('.ea-drawer-header').clientHeight;
		const footerHeight = ref.current.querySelector('.ea-drawer-footer').clientHeight;
		setHeight(headerHeight + footerHeight);
	})

	/**
	 * Hide the dropdown
	 * @param {Event} ev - Event
	 */
	function onOutside(ev) {
		onClose && onClose();
	}

	return createPortal(
		<ClickOutside className={classNames('ea-drawer-container', className)} onOutside={onOutside}>
			<div className="ea-drawer" role='dialog' tabIndex={-1} ref={ref}>
				<div className="ea-drawer-inner">
					<div className="ea-drawer-header">
						<h3 className="ea-drawer-title">Drawer Title</h3>
						<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true">
							<path d="M11.414 10l4.293-4.293a.999.999 0 1 0-1.414-1.414L10 8.586 5.707 4.293a.999.999 0 1 0-1.414 1.414L8.586 10l-4.293 4.293a.999.999 0 1 0 1.414 1.414L10 11.414l4.293 4.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z"></path>
						</svg>
					</div>

					<div className="ea-drawer-body" style={{height : (`calc(100% - ${height}px)`)}}>
						{children}
					</div>

					<div className="ea-drawer-footer">
						<Button isSecondary={true}>Cancel</Button>
						<Button isPrimary={true}>Submit</Button>
					</div>

				</div>
			</div>
		</ClickOutside>,
		getPortal('ea-drawer-portal')
	);
}

export default Drawer;
