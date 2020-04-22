/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
/**
 * External dependencies
 */
import { Spinner } from '@eaccounting/components';
import classNames from 'classnames';

const withPreloader = () => {
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
				this.state = {
					isLoading: false,
				};
				this.startLoading = this.startLoading.bind(this);
				this.finisLoading = this.finisLoading.bind(this);
				this.isLoading = this.isLoading.bind(this);
			}

			startLoading() {
				this.setState({
					isLoading: true,
				});
			}

			finisLoading() {
				this.setState({
					isLoading: false,
				});
			}

			isLoading() {
				const { isRequesting, isLoading, status } = this.props;
				return isLoading || isRequesting || this.state.isLoading || status === 'STATUS_IN_PROGRESS';
			}

			render() {
				const className = classNames('ea-preloader', {
					'is-loading': this.isLoading(),
				});
				return (
					<div className={className}>
						<WrappedComponent
							{...this.props}
							isLoading={this.isLoading()}
							startLoading={this.startLoading}
							finisLoading={this.finisLoading}
						/>
						{this.isLoading() && (
							<div className="ea-preloader-inner">
								<Spinner />
							</div>
						)}
					</div>
				);
			}
		}

		return Hoc;
	}, 'withPreloader');
};

export default withPreloader;
