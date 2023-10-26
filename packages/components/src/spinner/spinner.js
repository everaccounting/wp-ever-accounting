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

function Spinner( { active, className, text, fullscreen, children, props } ) {
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
		if ( fullscreen && active ) {
			return {
				position: 'fixed',
				top: 0,
				right: 0,
				bottom: 0,
				left: 0,
				zIndex: 99999,
			};
		}
		if ( active ) {
			return {
				position: 'relative',
			};
		}
		return {};
	};

	return (
		<div style={ { ...getStyle() } }>
			{ active && (
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
							'is-full-screen': fullscreen,
							'is-active': active,
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
			) }
			{ children }
		</div>
	);
}

export default Spinner;
