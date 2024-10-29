import NumberFormat from "react-number-format";
import "react-datepicker/dist/react-datepicker.css";
import {TextControl} from "@wordpress/components";

export function Datepicker(){


	return (
		<div>
			<TextControl
				label="Date"
				value={date}
				onChange={date => setDate(date)}
				type="date"
			/>
			<TextControl
				label="Amount"
				value={amount}
				onChange={amount => setAmount(amount)}
				type="number"
			/>
			<NumberFormat
				value={amount}
				displayType={'text'}
				thousandSeparator={true}
				prefix={'$'}
			/>
		</div>
	)
}
