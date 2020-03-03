import {combineReducers} from 'redux';
import {connectRouter} from 'connected-react-router'
import {revenues} from "./revenues";
const createRootReducer = (history) => combineReducers({
	router: connectRouter(history),
	revenues
});

export default createRootReducer;

