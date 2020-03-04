import {combineReducers} from 'redux';
import {transactions} from "./transactions";
import {revenues} from "./revenues";
import {revenue} from "./revenue";

const createRootReducer = combineReducers({
	transactions,
	revenues,
	revenue
});

export default createRootReducer;

