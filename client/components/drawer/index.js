import {useState, useEffect, createPortal, useRef} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import ClickOutside from "../click-outside";
import {getPortal} from "../lib";
import {CSSTransition} from 'react-transition-group';
import {Button} from "@wordpress/components";

import './style.scss';


function useDelayUnmount(isMounted, delayTime) {
	const [shouldRender, setShouldRender] = useState(false);

	useEffect(() => {
		let timeoutId;
		if (isMounted && !shouldRender) {
			setShouldRender(true);
		} else if (!isMounted && shouldRender) {
			timeoutId = setTimeout(() => setShouldRender(false), delayTime);
		}
		return () => clearTimeout(timeoutId);
	}, [isMounted, delayTime, shouldRender]);
	return shouldRender;
}


function Drawer(props) {
	const {children, className, title, onClose} = props;
	const [height, setHeight] = useState(0);
	const ref = useRef(null)

	useEffect(() => {
		const headerHeight = ref.current.querySelector('.ea-drawer-header').clientHeight;
		const footerHeight = ref.current.querySelector('.ea-drawer-footer').clientHeight;
		setHeight(headerHeight + footerHeight);
	})

	// const shouldRenderChild = useDelayUnmount(isMounted, 500);
	// const mountedStyle = { animation: "inAnimation 500ms ease-in" };
	// const unmountedStyle = { animation: "outAnimation 510ms ease-in" };

	/**
	 * Hide the dropdown
	 * @param {Event} ev - Event
	 */
	function onOutside(ev) {
		onClose && onClose();
	}

	return createPortal(
		<CSSTransition
			in={!!children}
			appear={true}
		>
			<ClickOutside className={classNames('ea-drawer', className)} role='dialog' tabIndex={-1} onOutside={onOutside}>
				<div className="ea-drawer-inner" ref={ref}>
					<div className="ea-drawer-header">
						<p className="ea-drawer-title">{title}</p>
						<svg viewBox="0 0 20 20" focusable="false" aria-hidden="true">
							<path d="M11.414 10l4.293-4.293a.999.999 0 1 0-1.414-1.414L10 8.586 5.707 4.293a.999.999 0 1 0-1.414 1.414L8.586 10l-4.293 4.293a.999.999 0 1 0 1.414 1.414L10 11.414l4.293 4.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z"></path>
						</svg>
					</div>

					<div className="ea-drawer-body" style={{height: (`calc(100% - ${height}px)`)}}>
						{children}
					</div>

					<div className="ea-drawer-footer">
						<Button isSecondary={true}>Cancel</Button>
						<Button isPrimary={true}>Submit</Button>
						<Button isSecondary={true}>Cancel</Button>
						<Button isPrimary={true}>Submit</Button>
					</div>
				</div>
			</ClickOutside></CSSTransition>,
		getPortal('ea-drawer-portal')
	);
}

export default Drawer;
