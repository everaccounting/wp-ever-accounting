import {Component} from '@wordpress/element';
import withTable from "./withTable";

class Table extends Component{

	constructor(props) {
		super(props);
		props.setMount('contacts', {});
	}

	render() {
		console.log(this.props);
		return(
			<div>
				FROM Table
			</div>
		)
	}
}

const Data = [
	{
		id: 1,
		name: 'Krunal'

	},
	{
		id: 2,
		name: 'Ankit'
	},
	{
		id: 3,
		name: 'Rushabh'
	}
];

export default withTable(Table, Data);
