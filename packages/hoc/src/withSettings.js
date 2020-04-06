/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {withSelect} from '@wordpress/data';

const withSettings = () => {
	return createHigherOrderComponent(WrappedComponent => {
		class Hoc extends Component {
			constructor(props) {
				super(props);
			}


			render() {
				return <WrappedComponent
					{...this.props}/>
			};
		}

		return withSelect((select) => {
			const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
			return {
				settings: fetchAPI('settings', {}),
				isLoading: isRequestingFetchAPI('settings', {}),
			}
		})(Hoc);
	}, 'withSettings');
};

export default withSettings;
