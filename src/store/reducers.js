import {combineReducers} from 'redux';
import {connectRouter} from 'connected-react-router'
import {revenues} from "./revenues";
import {revenue} from "./revenue";

const createRootReducer = (history) => combineReducers({
	router: connectRouter(history),
	revenues,
	revenue
});

export default createRootReducer;

