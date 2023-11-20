/**
 * Internal dependencies
 */
import './style.scss';
/**
 * WordPress dependencies
 */
import { useEffect } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';

function Spinner( { isActive, className, text, fullscreen, children, props } ) {
	const disableScroll = () => document.body.style.setProperty( 'overflow', 'hidden' );
	const enableScroll = () => document.body.style.setProperty( 'overflow', 'auto' );
	useEffect( () => {
		if ( fullscreen ) {
			disableScroll();
		} else {
			enableScroll();
		}

		return () => {
			enableScroll();
		};
	}, [ fullscreen ] );

	const getStyle = () => {
		if ( fullscreen ) {
			return {
				position: 'fixed',
				top: 0,
				right: 0,
				bottom: 0,
				left: 0,
				zIndex: 99999,
			};
		}
		return {
			position: 'relative',
		};
	};

	if ( isActive ) {
		return (
			<div style={ { ...getStyle() } }>
				<div
					style={ {
						display: 'block',
						position: 'absolute',
						zIndex: 657,
						backgroundColor: 'rgba(255, 255, 255, 0.901961)',
						margin: 0,
						top: 0,
						right: 0,
						bottom: 0,
						left: 0,
					} }
				>
					<div
						className={ classNames( 'eac-spinner', className, {
							'eac-spinner--full-screen': fullscreen,
							'eac-spinner--active': isActive,
						} ) }
						style={ {
							position: 'absolute',
							display: 'inline-block',
							left: 0,
						} }
						{ ...props }
					>
						<svg className="circular" viewBox="25 25 50 50">
							<circle className="path" cx="50" cy="50" r="20" fill="none" />
						</svg>
						{ text && <p className="eac-spinner__text">{ text }</p> }
					</div>
				</div>
				{ children }
			</div>
		);
	}

	return <>{ children }</>;
}

export default Spinner;
