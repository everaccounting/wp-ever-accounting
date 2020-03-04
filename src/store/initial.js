import {initialRevenues} from "./revenues";
import {initialRevenue} from "./revenue";
import {initialTransactions} from "./transactions";

export default function getInitialState() {
	return {
		transactions: initialTransactions,
		revenues: initialRevenues,
		revenue: initialRevenue,
	}
}
