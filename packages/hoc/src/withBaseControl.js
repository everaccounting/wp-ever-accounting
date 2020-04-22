/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import { BaseControl } from '@wordpress/components';
/**
 * External dependencies
 */
import classNames from 'classnames';
import PropTypes from 'prop-types';
import cuid from 'cuid';

export const withBaseControl = createHigherOrderComponent(WrappedComponent => {
	class Hoc extends Component {
		constructor(props) {
			super(props);
			this.state = {
				value: '',
			};
		}

		render() {
			const { label, help, className, before, after, required } = this.props;
			const classes = classNames('ea-form-group', className, {
				required: !!required,
			});
			console.log(this.props);
			const id = `inspector-ea-input-group-${cuid()}`;
			const describedby = [];

			if (help) {
				describedby.push(`${id}__help`);
			}
			if (before) {
				describedby.push(`${id}__before`);
			}
			if (after) {
				describedby.push(`${id}__after`);
			}

			return (
				<BaseControl label={label} id={id} help={help} className={classes}>
					<div className="ea-input-group">
						{before && (
							<span id={`${id}__before`} className="ea-input-group__before">
								{before}
							</span>
						)}
						<WrappedComponent {...this.props} aria-describedby={describedby.join(' ')} />
						{after && (
							<span id={`${id}__after`} className="ea-input-group__after">
								{after}
							</span>
						)}
					</div>
				</BaseControl>
			);
		}
	}

	Hoc.propTypes = {
		className: PropTypes.string,
		label: PropTypes.string,
		help: PropTypes.string,
		before: PropTypes.node,
		after: PropTypes.node,
	};
	return Hoc;
}, 'withBaseControl');

export default withBaseControl;
