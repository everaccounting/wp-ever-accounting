/**
 * WordPress dependencies
 */
import { Suspense, useRef, useCallback } from '@wordpress/element';
/**
 * External dependencies
 */
import classnames from 'classnames';
import { Spinner } from '@wordpress/components';
/**
 * Internal dependencies
 */
import './style.scss';

export const Panel = ( {
	content,
	isPanelOpen,
	isPanelSwitching,
	title,
	closePanel,
} ) => {
	const containerRef = useRef( null );

	const classNames = classnames( 'eaccounting__panel-wrapper', {
		'is-open': isPanelOpen,
		'is-switching': isPanelSwitching,
	} );

	// const mergedContainerRef = useCallback( ( node ) => {
	// 	containerRef.current = node;
	// 	focusOnMountRef( node );
	// }, [] );

	return (
		<div
			className={ classNames }
			tabIndex={ 0 }
			role="tabpanel"
			aria-label={ title }
		>
			<Suspense fallback={ <Spinner /> }>{ content }</Suspense>
		</div>
	);
};

export default Panel;
