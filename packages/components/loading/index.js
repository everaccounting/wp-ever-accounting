import { Fragment, Component } from '@wordpress/element';
import classNames from 'classnames';

import PropTypes from 'prop-types';
import './style.scss';

export default class Loading extends Component {
	constructor( props ) {
		super( props );
	}

	documentBody() {
		return document.body;
	}

	disableScroll() {
		const documentBody = this.documentBody();
		if ( documentBody ) {
			documentBody.style.setProperty( 'overflow', 'hidden' );
		}
	}

	enableScroll() {
		const documentBody = this.documentBody();
		if ( documentBody ) {
			documentBody.style.removeProperty( 'overflow' );
		}
	}

	getStyle() {
		const { loading, fullscreen } = this.props;

		if ( fullscreen ) {
			this.disableScroll();
			return {
				position: 'fixed',
				top: 0,
				right: 0,
				bottom: 0,
				left: 0,
				zIndex: 99999,
			};
		}

		this.enableScroll();

		if ( loading ) {
			return {
				position: 'relative',
			};
		}
		return {};
	}

	render() {
		const { loading, fullscreen, text } = this.props;
		return (
			<div style={ this.getStyle() }>
				{ loading && (
					<div
						style={ {
							display: 'block',
							position: 'absolute',
							zIndex: 657,
							backgroundColor: 'rgba(255, 255, 255, 0.6)',
							margin: 0,
							top: 0,
							right: 0,
							bottom: 0,
							left: 0,
						} }
					>
						<div
							className={ classNames( 'ea-loading-spinner', {
								'is-full-screen': fullscreen,
							} ) }
							style={ {
								position: 'absolute',
							} }
						>
							<svg
								className="loader"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 91.3 91.1"
							>
								<circle cx="45.7" cy="45.7" r="45.7" />
								<circle
									fill="#FFF"
									cx="45.7"
									cy="24.4"
									r="12.5"
								/>
							</svg>
							{ text && (
								<p className="ea-loading-text">{ text }</p>
							) }
						</div>
					</div>
				) }

				{ this.props.children }
			</div>
		);
	}
}

Loading.propTypes = {
	loading: PropTypes.bool,
	fullscreen: PropTypes.bool,
	text: PropTypes.string,
};

Loading.defaultProps = {
	loading: true,
};
