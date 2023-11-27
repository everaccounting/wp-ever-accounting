/**
 * External dependencies
 */
import classnames from 'classnames';
/**
 * Internal dependencies
 */
import Body from './body';
import './style.scss';

function Panel( props ) {
	const { className, title, actions, children, footer } = props;
	const classes = classnames( 'eac-panel', className );

	const renderChildren = () => {
		if ( children && typeof children === 'string' ) {
			return <Body>{ children }</Body>;
		}

		return children;
	};
	return (
		<div className={ classes }>
			{ title || actions ? (
				<div className="eac-panel__header">
					{ title && <div className="eac-panel__title">{ title }</div> }
					{ actions && <div className="eac-panel__actions">{ actions }</div> }
				</div>
			) : null }
			{ renderChildren() }
			{ footer && <div className="eac-panel__footer">{ footer }</div> }
		</div>
	);
}
Panel.Body = Body;
export default Panel;
