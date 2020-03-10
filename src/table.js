import { Component } from '@wordpress/element';
import withTable from './withTable';
import { Button } from '@wordpress/components';
class Table extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { items = [], isLoading, total } = this.props;
		return (
			<div>
				{total && <p>{total}</p>}
				{isLoading && <p>Loading</p>}
				<input
					type="number"
					onChange={e => {
						this.props.setPageChange(e.target.value);
					}}
				/>
				<ul>
					{items.map(item => {
						return (
							<li key={item.id}>
								{item.first_name} {item.last_name} <Button onClick={() => this.props.setDelete(item)}>X</Button>
							</li>
						);
					})}
				</ul>
			</div>
		);
	}
}

export default withTable(Table);
