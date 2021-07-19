/**
 * WordPress dependencies
 */
import { Suspense, useRef, useCallback } from '@wordpress/element';
import { Spinner, Fill } from '@wordpress/components';
/**
 * External dependencies
 */
import classnames from 'classnames';
import { Text } from '@eaccounting/components';
/**
 * Internal dependencies
 */
import './style.scss';
import PanelHeader from './header';
function Panel( props ) {
	// eslint-disable-next-line no-unused-vars
	const { className, title, subtitle, menu, content, closePanel } = props;
	const containerRef = useRef( null );
	const mergedContainerRef = useCallback( ( node ) => {
		containerRef.current = node;
		//focusOnMountRef( node );
	}, [] );

	const classNames = classnames( className, {} );

	const finishTransition = ( e ) => {
		if ( e && e.propertyName === 'transform' ) {
			// clearPanel();
			// possibleFocusPanel();
		}
	};

	const headerClassNames = classnames(
		'woocommerce-layout__activity-panel-header',
		{
			'has--subtitle': !! subtitle,
		}
	);
	console.log( headerClassNames );
	return (
		<Fill name="panel">
			<div
				className={ classNames }
				tabIndex={ 0 }
				role="tabpanel"
				onTransitionEnd={ finishTransition }
				ref={ mergedContainerRef }
			>
				{ ( !! title || !! subtitle ) && (
					<div className={ headerClassNames }>
						<div className="woocommerce-layout__activity-panel-header-title">
							<Text variant="title.small">{ title }</Text>
						</div>
						<div className="woocommerce-layout__activity-panel-header-subtitle">
							{ subtitle && (
								<Text variant="body.small">{ subtitle }</Text>
							) }
						</div>
						{ menu && (
							<div className="woocommerce-layout__activity-panel-header-menu">
								{ menu }
							</div>
						) }
					</div>
				) }

				<div className="woocommerce-layout__activity-panel-content">
					{ props.children && (
						<Suspense fallback={ <Spinner /> }>
							{ props.children }
						</Suspense>
					) }
				</div>
			</div>
		</Fill>
	);
}

Panel.PanelHeader = PanelHeader;
export default Panel;
