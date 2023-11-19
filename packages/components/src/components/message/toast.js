/**
 * Internal dependencies
 */
import icons from './assets';
/**
 * WordPress dependencies
 */
import { useEffect, useRef, useState } from '@wordpress/element';

export default function Toast( props ) {
	const [ visible, setVisible ] = useState( false );
	const timeoutRef = useRef( null );

	useEffect( () => {
		setVisible( true );

		startTimer();

		return () => {
			stopTimer();
		};
	}, [] );

	const onClose = () => {
		stopTimer();
		setVisible( false );
	};

	const startTimer = () => {
		if ( props.duration > 0 ) {
			timeoutRef.current = setTimeout( () => {
				onClose();
			}, props.duration );
		}
	};

	const stopTimer = () => {
		clearTimeout( timeoutRef.current );
	};

	return (
		<div className={ `el-message ${ props.customClass }` } onMouseEnter={ stopTimer } onMouseLeave={ startTimer }>
			{ ! props.iconClass && <img alt={ props.type } className="el-message__img" src={ icons[ props.type ] } /> }
			<div className={ `el-message__group ${ props.iconClass ? 'is-with-icon' : '' }` }>
				{ props.iconClass && <i className={ `el-message__icon ${ props.iconClass }` }></i> }
				<p>{ props.message }</p>
				{ props.showClose && <div className="el-message__closeBtn el-icon-close" onClick={ onClose } aria-label="Close" role="button"></div> }
			</div>
		</div>
	);
}
