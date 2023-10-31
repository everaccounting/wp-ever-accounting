/**
 * Internal dependencies
 */
import './style.scss';

export const Badge = ( { count, className = '', ...props } ) => {
	return (
		<span className={ `eac-badge ${ className }` } { ...props }>
			{ count }
		</span>
	);
};

export default Badge;
