import {STATUS_COMPLETE} from "status";

export const initialRevenue = {
	id: null,
	account_id: '',
	account: {},
	paid_at: '',
	amount: '',
	contact_id: '',
	description: '',
	category_id: '',
	reference: '',
	payment_method: '',
	attachment_url: '',
	parent_id: '',
	reconciled: '0',
	status: STATUS_COMPLETE
};

export {default as revenue} from './reducers';
export * from './actions';
