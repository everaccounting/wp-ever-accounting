import {Component, Fragment} from "@wordpress/element";
import {withTable, Spinner, SearchBox, Button, TableNav} from "@eaccounting/components";
import { getHeaders, getBulk } from './constants';
import {__} from "@wordpress/i18n";

class Table extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		console.log(props);
	}

	componentDidCatch(error, info) {
		this.setState({error: true, stack: error, info});
	}

	render() {
		const {items,selected,page,per_page, total, isLoading} = this.props;
		return (
			<Fragment>
				{isLoading && <Spinner/>}
				<div className="ea-table-display">
					<Button className="page-title-action" onClick={this.onAdd}>
						{__('Add Category')}
					</Button>
					<SearchBox isLoading={isLoading} onSearch={this.props.setSearch}/>
				</div>
				<TableNav
					page={page}
					per_page={per_page}
					total={total}
					selected={selected}
					onChangePage={this.props.setPageChange}
					onAction={this.props.setAction}
					isLoading={isLoading}
					bulk={getBulk()}
				/>

			</Fragment>
		);
	}
}

export default withTable(Table)
