import {registerStore} from '@wordpress/data';
import {controls as dataControls} from '@wordpress/data-controls';

export const STORE_COLLECTION_KEY = 'ea/table';
import * as selectors from './selectors';
import * as actions from './actions';
import * as resolvers from './resolvers';
import reducer from './reducers';
import {controls} from './controls';

registerStore(STORE_COLLECTION_KEY, {
	reducer,
	actions,
	controls: {...dataControls, ...controls},
	selectors,
	resolvers,
});
