/**
 * External dependencies
 */
import classnames from 'classnames';
function Body( { className, children } ) {
	const classes = classnames( 'eac-panel__body', className );
	return <div className={ classes }>{ children }</div>;
}

export default Body;
