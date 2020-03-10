import {Component} from '@wordpress/element';
import withTable from "./withTable";

class Table extends Component{

	constructor(props) {
		super(props);
	}

	render() {
		const {items = [] } = this.props;
		return(
			<div>
				<input type="number" onChange={(e)=> {
					this.props.setPageChange(e.target.value)
				}}/>
				<ul>
					{items.map((item)=> {
						return(<li key={item.id}>{item.first_name} {item.last_name}</li>)
					})}
				</ul>
			</div>
		)
	}
}


export default withTable(Table);
