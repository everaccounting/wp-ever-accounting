import { initialRevenue } from './index';
import { STATUS_IN_PROGRESS, STATUS_COMPLETE, STATUS_FAILED } from 'status';

const revenue = (state = initialRevenue, action) => {
	switch (action.type) {
		case 'REVENUE_LOADING':
			return { ...state, status: STATUS_IN_PROGRESS };

		case 'REVENUE_SUCCESS':
			return { ...state, status: STATUS_COMPLETE, ...action.payload.data };

		case 'REVENUE_FAILED':
			return { ...state, status: STATUS_FAILED };
		default:
			return state;
	}
};

export default revenue;
