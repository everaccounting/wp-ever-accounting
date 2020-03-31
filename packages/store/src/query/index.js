import {registerStore} from '@wordpress/data';

import {REDUCER_KEY} from "./constants";
import reducer from "./reducer";
import * as actions from "./actions";
import * as selectors from "./selectors";

export default registerStore(REDUCER_KEY, {
	reducer,
	actions,
	selectors: {...selectors},
});

export const QUERY_KEY = REDUCER_KEY;
