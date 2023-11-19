/**
 * WordPress dependencies
 */
import { Icon } from '@wordpress/components';
/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { checkFilled, infoFilled, warningFilled, alertFilled } from '../icons';
import './style.scss';
import unauthorized from './unauthorized';
import notFound from './not-found';
import serverError from './server-error';

export const IconMap = {
	success: checkFilled,
	error: alertFilled,
	info: infoFilled,
	warning: warningFilled,
};
export const ExceptionMap = {
	404: notFound,
	500: serverError,
	403: unauthorized,
	'not-found': notFound,
	'server-error': serverError,
	unauthorized,
};

const IconComponent = ( { status, icon } ) => {
	if ( Object.keys( ExceptionMap ).includes( `${ status }` ) ) {
		const SVGComponent = ExceptionMap[ status ];
		return (
			<div className="eac-result__image">
				<SVGComponent />
			</div>
		);
	}

	if ( Object.keys( IconMap ).includes( `${ status }` ) ) {
		return (
			<div className="eac-result__icon">
				<Icon icon={ IconMap[ status ] } size={ 100 } />
			</div>
		);
	}

	if ( icon && typeof icon === 'string' ) {
		return (
			<div className="eac-result__icon">
				<Icon icon={ icon } size={ 100 } />
			</div>
		);
	}

	return null;
};

export function Result( { className, title, subTitle, extra, style, children, status = 'info', icon, props } ) {
	const classes = classNames( 'eac-result', className, {
		[ `eac-result--${ status }` ]: status,
	} );
	return (
		<div className={ classes } style={ style } { ...props }>
			<IconComponent status={ status } icon={ icon } />
			{ title && <div className="eac-result__title">{ title }</div> }
			{ subTitle && <div className="eac-result__subtitle">{ subTitle }</div> }
			{ extra && <div className="eac-result__extra">{ extra }</div> }
			{ children && <div className="eac-result__content">{ children }</div> }
		</div>
	);
}

export default Result;
