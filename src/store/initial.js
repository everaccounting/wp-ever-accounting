import {initialRevenues} from "./revenues";
import {initialRevenue} from "./revenue";

export default function getInitialState() {
	return {
		revenues: initialRevenues,
		revenue: initialRevenue,
	}
}
