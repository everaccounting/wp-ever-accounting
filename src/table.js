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


export default withTable(Table);
