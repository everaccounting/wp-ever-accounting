import { Component } from 'react';
import { Spinner as Base } from '@wordpress/components';
import classnames from 'classnames';
import propTypes from 'prop-types';

export default class Spinner extends Component {
	static propTypes = {
		className: propTypes.string,
		text: propTypes.string,
		padding: propTypes.number,
		align: propTypes.string,
	};

	static defaultProps = {
		text: '',
		padding: 20,
		align: 'center',
	};

	render() {
		const classes = classnames({
			'ea-spinner': true,
		});
		const style = {
			padding: this.props.padding + 'px',
			textAlign: this.props.align,
		};
		return (
			<div className={classes} style={style}>
				<Base />
				<p>{this.props.text}</p>
			</div>
		);
	}
}
