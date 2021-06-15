import {registerStore} from "@wordpress/data";
import {STORE_NAME} from "./constants";
import reducer from "./reducer";
import controls from "../controls";
import * as actions from "./actions";
import * as selectors from "./selectors";
import * as resolvers from "./resolvers";

registerStore(STORE_NAME, {
	reducer,
	controls: controls,
	actions: actions,
	selectors: selectors,
	resolvers: resolvers,
});

export const SETTINGS_STORE_NAME = STORE_NAME;
