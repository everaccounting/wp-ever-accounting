/**
 * WordPress dependencies
 */
import {createHigherOrderComponent, compose} from '@wordpress/compose';
import {withDispatch, withSelect} from '@wordpress/data';

const withData = createHigherOrderComponent(
	compose([
		withSelect(
			(select, ownProps) => {
				console.log(ownProps);
				const {fetchAPI, isRequestingFetchAPI} = select('ea/collection');
				return {
					settings: fetchAPI('settings', {}),
					isLoading: isRequestingFetchAPI('settings', {}),
				}
			}
		),
		withDispatch(
			(dispatch) => {

			}
		),
	]),
	'withData'
);

export default withData;
