import Spinner from '../spinner';
import classnames from 'classnames';
import PropTypes from "prop-types";

export default function Blocker(props) {
	const {isBlocked = false, children, className } = props;
	const classes = classnames('ea-blocker', className, {
		isBlocked: !!isBlocked,
	});

	return(
		<div className={classes}>
			{children}
			{isBlocked && <div className='ea-blocker-loading'><Spinner/></div>}
		</div>
	)
}
Blocker.Blocker = {
	isBlocked: PropTypes.bool.isRequired,
};
