/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';
import icon from './icon.svg';

function Empty( props ) {
	const { className, image = icon, title: _title, children, style, ...restProps } = props;
	const title = typeof _title !== 'undefined' ? _title : null;
	const alt = typeof _title === 'string' ? _title : 'empty';
	const imageNode = typeof image === 'string' ? <img src={ image } alt={ alt } /> : image;

	return (
		<div className={ classNames( 'eac-empty', className ) } style={ style } { ...restProps }>
			{ imageNode && <div className="eac-empty__image">{ imageNode }</div> }
			{ title && <div className="eac-empty__title">{ title }</div> }
			{ children && <div className="eac-empty__children">{ children }</div> }
		</div>
	);
}

export default Empty;
