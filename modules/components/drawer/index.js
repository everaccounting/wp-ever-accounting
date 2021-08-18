/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * WordPress dependencies
 */
import { Suspense, useRef, useCallback } from '@wordpress/element';
import { Fill, Icon, Button } from '@wordpress/components';

/**
 * Internal dependencies
 */
import useFocusOnMount from '../hooks/useFocusOnMount';
import useFocusOutside from '../hooks/useFocusOutside';
import { Text } from '../experimental';
import './style.scss';
import PropTypes from 'prop-types';

function Drawer(props) {
	const { className, onClickOutSide, title, renderToolbar } = props;
	const containerRef = useRef(null);
	const useFocusOutsideProps = useFocusOutside(onClickOutSide);
	const focusOnMountRef = useFocusOnMount();
	const mergedContainerRef = useCallback((node) => {
		containerRef.current = node;
		focusOnMountRef(node);
	}, []);

	const possibleFocusPanel = () => {
		if (!containerRef.current) {
			return;
		}

		focusOnMountRef(containerRef.current);
	};

	const finishTransition = (e) => {
		if (e && e.propertyName === 'transform') {
			possibleFocusPanel();
		}
	};

	const classNames = classnames('eaccounting-drawer', className);
	return (
		<Fill name="drawer">
			<div
				className={classNames}
				tabIndex={0}
				role="tabpanel"
				onTransitionEnd={finishTransition}
				ref={mergedContainerRef}
				{...useFocusOutsideProps}
			>
				<div className="eaccounting-drawer__header">
					<div className="eaccounting-drawer__title">
						<Text style={{ fontSize: '1.2em' }}>{title}</Text>
					</div>
					<div className="eaccounting-drawer__header-toolbar">
						{renderToolbar && { renderToolbar }}
						<Button
							onClick={onClickOutSide}
							style={{ height: 'auto', padding: 0 }}
						>
							<Icon
								icon="exit"
								style={{ transform: 'rotate(180deg)' }}
							/>
						</Button>
					</div>
				</div>
				{props.children && <Suspense>{props.children}</Suspense>}
			</div>
		</Fill>
	);
}

Drawer.propTypes = {
	className: PropTypes.string,
	title: PropTypes.oneOfType([
		PropTypes.string,
		PropTypes.node,
		PropTypes.func,
	]),
	renderToolbar: PropTypes.func,
	onClickOutSide: PropTypes.func.isRequired,
};

export default Drawer;
