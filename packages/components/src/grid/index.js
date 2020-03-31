/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

export const Row = props => {
	const classes = classnames('ea-row', props.className);
	return <div className={classes}>{props.children && props.children}</div>;
};
Row.propTypes = {
	className: PropTypes.string,
};

export const Col = props => {
	const { col = '6' } = props;
	const classes = 'ea-col-' + col;
	return <div className={classes}>{props.children && props.children}</div>;
};

Col.propTypes = {
	col: PropTypes.number,
};
